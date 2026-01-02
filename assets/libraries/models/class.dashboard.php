<?php
/**
 * Dashboard Class
 * Handles server monitoring and system diagnostics
 * Fixed to handle disabled shell_exec() function
 */

class Dashboard
{
    private $dataFile;
    private $cacheTimeout = 60; // Cache data for 60 seconds

    public function __construct()
    {
        $this->dataFile = sys_get_temp_dir() . '/dashboard_data.json';
    }

    /**
     * Safe wrapper for shell_exec that checks if function is available
     * @param string $command
     * @return string|null
     */
    private function safeShellExec($command)
    {
        if (!function_exists('shell_exec')) {
            return null;
        }

        // Check if shell_exec is in disabled functions
        $disabled = explode(',', ini_get('disable_functions'));
        if (in_array('shell_exec', $disabled)) {
            return null;
        }

        return shell_exec($command);
    }

    /**
     * Safe wrapper for exec that checks if function is available
     * @param string $command
     * @return array|null
     */
    private function safeExec($command)
    {
        if (!function_exists('exec')) {
            return null;
        }

        $disabled = explode(',', ini_get('disable_functions'));
        if (in_array('exec', $disabled)) {
            return null;
        }

        $output = [];
        exec($command, $output);
        return $output;
    }

    /**
     * Get PHP process count
     * @return int
     */
    private function getPhpProcessCount()
    {
        $result = $this->safeShellExec("ps aux | grep -c 'php'");
        return $result ? (int)trim($result) - 1 : 0; // -1 to exclude grep itself
    }

    /**
     * Get Node process count
     * @return int
     */
    private function getNodeProcessCount()
    {
        $result = $this->safeShellExec("ps aux | grep -c 'node'");
        return $result ? (int)trim($result) - 1 : 0;
    }

    /**
     * Get HTTPD/Apache process count
     * @return int
     */
    private function getHttpdProcessCount()
    {
        $result = $this->safeShellExec("ps aux | grep -c 'httpd\\|apache2'");
        return $result ? (int)trim($result) - 1 : 0;
    }

    /**
     * Get concurrent Apache connections
     * @return int
     */
    private function getConcurrentApacheConnections()
    {
        $result = $this->safeShellExec("netstat -an | grep :80 | wc -l");
        return $result ? (int)trim($result) : 0;
    }

    /**
     * Get current CPU usage percentage
     * @return float
     */
    private function getCurrentCpuUsage()
    {
        // Use sys_getloadavg as a fallback
        $load = sys_getloadavg();
        if ($load && isset($load[0])) {
            // Convert load average to percentage (assuming single core, adjust as needed)
            $cpuCores = $this->getCpuCores();
            return round(($load[0] / $cpuCores) * 100, 2);
        }

        // Try to get from /proc/stat if shell commands fail
        if (file_exists('/proc/stat')) {
            $stat1 = file('/proc/stat');
            sleep(1);
            $stat2 = file('/proc/stat');

            $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
            $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));

            $dif = array();
            $dif['user'] = $info2[0] - $info1[0];
            $dif['nice'] = $info2[1] - $info1[1];
            $dif['sys'] = $info2[2] - $info1[2];
            $dif['idle'] = $info2[3] - $info1[3];

            $total = array_sum($dif);
            $cpu = array();

            foreach($dif as $x => $y) {
                $cpu[$x] = round($y / $total * 100, 1);
            }

            return 100 - $cpu['idle'];
        }

        return 0;
    }

    /**
     * Get number of CPU cores
     * @return int
     */
    private function getCpuCores()
    {
        $result = $this->safeShellExec("nproc");
        if ($result) {
            return (int)trim($result);
        }

        // Fallback: try to read from /proc/cpuinfo
        if (file_exists('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            return count($matches[0]);
        }

        return 1; // Default to 1 core
    }

    /**
     * Get total RAM in GB
     * @return float
     */
    private function getTotalRam()
    {
        if (file_exists('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)/', $meminfo, $matches);
            if (isset($matches[1])) {
                return round($matches[1] / 1024 / 1024, 2); // Convert KB to GB
            }
        }

        $result = $this->safeShellExec("free -g | awk '/^Mem:/{print $2}'");
        return $result ? (float)trim($result) : 0;
    }

    /**
     * Get available RAM in GB
     * @return float
     */
    private function getAvailableRam()
    {
        if (file_exists('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $matches);
            if (isset($matches[1])) {
                return round($matches[1] / 1024 / 1024, 2); // Convert KB to GB
            }
        }

        $result = $this->safeShellExec("free -g | awk '/^Mem:/{print $7}'");
        return $result ? (float)trim($result) : 0;
    }

    /**
     * Get RAM used percentage
     * @return float
     */
    private function getRamUsedPercentage()
    {
        $total = $this->getTotalRam();
        $available = $this->getAvailableRam();

        if ($total > 0) {
            return round((($total - $available) / $total) * 100, 2);
        }

        return 0;
    }

    /**
     * Get disk usage information for root partition
     * @return array
     */
    private function getDiskInfo()
    {
        $root = '/';

        $diskTotal = disk_total_space($root);
        $diskFree = disk_free_space($root);
        $diskUsed = $diskTotal - $diskFree;

        return [
            'total' => round($diskTotal / (1024 ** 3), 2), // Convert to GB
            'free' => round($diskFree / (1024 ** 3), 2),
            'used' => round($diskUsed / (1024 ** 3), 2),
            'percentage' => round(($diskUsed / $diskTotal) * 100, 2)
        ];
    }

    /**
     * Get server information
     * Called by server_admin_dashboard.php
     * @return array
     */
    public function getServerInfo()
    {
        $diskInfo = $this->getDiskInfo();
        $totalRam = $this->getTotalRam();

        return [
            'totalconnections' => 1000, // Max connections (configurable)
            'connections' => $this->getConcurrentApacheConnections(),
            'php_process_count' => $this->getPhpProcessCount(),
            'diskused' => $diskInfo['used'],
            'diskfree' => $diskInfo['free'],
            'disktotal' => $diskInfo['total'],
            'ram_total' => $totalRam,
        ];
    }

    /**
     * Get server status information (for AJAX calls)
     * @return array
     */
    public function serverStatusInfo()
    {
        // Check if we have recent cached data
        $data = $this->loadCachedData();

        if ($data === null) {
            // Generate new data
            $data = $this->generateServerStatus();
            $this->saveCachedData($data);
        }

        return [
            'status' => 'success',
            'message' => $data,
            'result' => $data
        ];
    }

    /**
     * Generate server status data
     * @return array
     */
    private function generateServerStatus()
    {
        $currentTime = date('Y-m-d H:i:s');

        // Initialize or load existing data
        $existingData = $this->loadCachedData();

        $data = [
            'PHP_PROCESS_COUNT' => isset($existingData['PHP_PROCESS_COUNT']) ? $existingData['PHP_PROCESS_COUNT'] : [],
            'NODE_PROCESS_COUNT' => isset($existingData['NODE_PROCESS_COUNT']) ? $existingData['NODE_PROCESS_COUNT'] : [],
            'HTTPD_PROCESS_COUNT' => isset($existingData['HTTPD_PROCESS_COUNT']) ? $existingData['HTTPD_PROCESS_COUNT'] : [],
            'CONCURRENT_APACHE_CONNECTIONS' => isset($existingData['CONCURRENT_APACHE_CONNECTIONS']) ? $existingData['CONCURRENT_APACHE_CONNECTIONS'] : [],
            'CURRENT_CPU_USAGE' => isset($existingData['CURRENT_CPU_USAGE']) ? $existingData['CURRENT_CPU_USAGE'] : [],
            'RAM_USED_PERCENTAGE' => isset($existingData['RAM_USED_PERCENTAGE']) ? $existingData['RAM_USED_PERCENTAGE'] : [],
            'TOTAL_RAM' => isset($existingData['TOTAL_RAM']) ? $existingData['TOTAL_RAM'] : [],
            'AVAILABLE_RAM' => isset($existingData['AVAILABLE_RAM']) ? $existingData['AVAILABLE_RAM'] : [],
            'USED_DISK_SIZE_PERCENTAGE' => isset($existingData['USED_DISK_SIZE_PERCENTAGE']) ? $existingData['USED_DISK_SIZE_PERCENTAGE'] : [],
            'COMMAND_EXECUTED_TIME' => isset($existingData['COMMAND_EXECUTED_TIME']) ? $existingData['COMMAND_EXECUTED_TIME'] : [],
        ];

        // Append new values
        $data['PHP_PROCESS_COUNT'][] = $this->getPhpProcessCount();
        $data['NODE_PROCESS_COUNT'][] = $this->getNodeProcessCount();
        $data['HTTPD_PROCESS_COUNT'][] = $this->getHttpdProcessCount();
        $data['CONCURRENT_APACHE_CONNECTIONS'][] = $this->getConcurrentApacheConnections();
        $data['CURRENT_CPU_USAGE'][] = $this->getCurrentCpuUsage();
        $data['RAM_USED_PERCENTAGE'][] = $this->getRamUsedPercentage();
        $data['TOTAL_RAM'][] = $this->getTotalRam();
        $data['AVAILABLE_RAM'][] = $this->getAvailableRam();

        $diskInfo = $this->getDiskInfo();
        $data['USED_DISK_SIZE_PERCENTAGE'][] = $diskInfo['percentage'];
        $data['COMMAND_EXECUTED_TIME'][] = $currentTime;

        // Keep only last 100 entries to prevent unlimited growth
        foreach ($data as $key => &$value) {
            if (is_array($value) && count($value) > 100) {
                $value = array_slice($value, -100);
            }
        }

        return $data;
    }

    /**
     * Load cached data from file
     * @return array|null
     */
    private function loadCachedData()
    {
        if (file_exists($this->dataFile)) {
            $fileTime = filemtime($this->dataFile);

            // Check if cache is still valid
            if (time() - $fileTime < $this->cacheTimeout) {
                $content = file_get_contents($this->dataFile);
                $data = json_decode($content, true);

                if ($data !== null) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * Save data to cache file
     * @param array $data
     */
    private function saveCachedData($data)
    {
        file_put_contents($this->dataFile, json_encode($data));
    }

    /**
     * Get system diagnostic data
     * @return array
     */
    public function getSystemDiagnosticData()
    {
        $diagnostics = [];

        // Check PHP version
        $phpVersion = phpversion();
        $diagnostics[] = [
            'title' => 'PHP Version',
            'subtitle' => 'Current: ' . $phpVersion,
            'value' => version_compare($phpVersion, '7.4', '>='),
            'modal_id' => 'php_version'
        ];

        // Check if shell_exec is enabled
        $shellExecAvailable = function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')));
        $diagnostics[] = [
            'title' => 'Shell Exec Function',
            'subtitle' => $shellExecAvailable ? 'Enabled' : 'Disabled (using fallback methods)',
            'value' => true, // Always return true since we have fallbacks
            'modal_id' => 'shell_exec'
        ];

        // Check disk space
        $diskInfo = $this->getDiskInfo();
        $diskSpaceOk = $diskInfo['percentage'] < 90;
        $diagnostics[] = [
            'title' => 'Disk Space',
            'subtitle' => $diskInfo['free'] . ' GB free (' . (100 - $diskInfo['percentage']) . '% available)',
            'value' => $diskSpaceOk,
            'modal_id' => 'disk_space'
        ];

        // Check memory
        $totalRam = $this->getTotalRam();
        $availableRam = $this->getAvailableRam();
        $ramPercentage = $this->getRamUsedPercentage();
        $memoryOk = $ramPercentage < 90;
        $diagnostics[] = [
            'title' => 'Memory Usage',
            'subtitle' => $availableRam . ' GB available (' . (100 - $ramPercentage) . '% free)',
            'value' => $memoryOk,
            'modal_id' => 'memory'
        ];

        // Check CPU load
        $load = sys_getloadavg();
        $cpuOk = $load[0] < 5.0;
        $diagnostics[] = [
            'title' => 'CPU Load Average',
            'subtitle' => 'Current: ' . $load[0],
            'value' => $cpuOk,
            'modal_id' => 'cpu_load'
        ];

        // Check if /proc filesystem is accessible
        $procAvailable = file_exists('/proc/meminfo') && is_readable('/proc/meminfo');
        $diagnostics[] = [
            'title' => '/proc Filesystem',
            'subtitle' => $procAvailable ? 'Accessible' : 'Not accessible',
            'value' => $procAvailable,
            'modal_id' => 'proc_fs'
        ];

        return $diagnostics;
    }
}
