<?php
  include($tconfig['tpanel_path'].$templatePath."footer/footer_home.php");
?>
 <? if($MODULES_OBJ->isEnableCookieConsent()) { ?>

    <script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/js/cookie-consent.js" charset="UTF-8"></script>
    <script type="text/javascript" charset="UTF-8">

        document.addEventListener('DOMContentLoaded', function () {
            cookieconsent.run({
                "notice_banner_type":"simple",
                "consent_type":"express",
                "palette":"dark",
                "language":"<?=$lang; ?>",
                "page_load_consent_levels":["strictly-necessary"],
                "notice_banner_reject_button_hide":false,
                "preferences_center_close_button_hide":false,
                "page_refresh_confirmation_buttons":false,
                "website_name": "<?= $tconfig['tsite_folder'] ?>",
                "website_privacy_policy_url": "<?= $tconfig['tsite_url'] ?>/privacy-policy"
            });
        });

        var cookie_count = 0;
        //setTimeout(function(){ $(".cc-nb-changep").hide(); }, 1000);
        cookie_css();

        function cookie_css(){
            if(cookie_count < 20){
                setTimeout(function(){
                    cookie_css();
                }, 100);
            }
            cookie_count++;

            $(".cc-nb-changep").hide();
            $(".termsfeed-com---nb .cc-nb-main-container").css("padding", "1.8rem");
            $(".termsfeed-com---nb .cc-nb-title").css("font-size", "18px");
            // $(".termsfeed-com---reset p").css("margin-bottom", "0.5rem");
            // $(".termsfeed-com---nb-simple").css("max-width", "30%");
            $(".cc-nb-okagree").text("<?= $langage_lbl['LBL_ACCEPT_TXT'] ?>");
            $(".cc-nb-reject").text("<?= $langage_lbl['LBL_DECLINE_TXT'] ?>");
        }
    </script>

    <noscript>ePrivacy and GPDR Cookie Consent by <a href="https://www.CookieConsent.com/" rel="nofollow noopener">Cookie Consent</a></noscript>
    <?php //End Cookie Consent by https://www.CookieConsent.com ?>
<? } ?> 

<?php if($MODULES_OBJ->isEnableGoogleAds() && !empty($GOOGLE_ADSENSE_CLIENT_ID) && !empty($GOOGLE_ADSENSE_AD_SLOT_ID)) { ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-<?= $GOOGLE_ADSENSE_CLIENT_ID ?>"
            crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
         style="z-index:12;background-color:white;display:inline-block;width:500px;height:300px;position:fixed;bottom:0;left:0;"
         data-ad-client="ca-pub-<?= $GOOGLE_ADSENSE_CLIENT_ID ?>"
         data-ad-slot="<?= $GOOGLE_ADSENSE_AD_SLOT_ID ?>"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
<?php } ?>

<?php if(!empty($FACEBOOK_PIXEL_ID)) { ?>
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= $FACEBOOK_PIXEL_ID ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $FACEBOOK_PIXEL_ID ?>&ev=PageView&noscript=1" /></noscript>
<?php } ?> 