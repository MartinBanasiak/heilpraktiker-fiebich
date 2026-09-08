const COOKIE_ESSENTIAL = "essential"
const COOKIE_EXTERNAL_MEDIA = "external_media";
const COOKIE_MARKETING = "marketing";
const COOKIE_STATISTICS = "statistics";

/**
 * All provider with iframe-, script-class or gtmVariable, which disables / activates the cookie include
 *
 * @type {*[]}
 */
const providers = [
    {
        id: 'awin',
        name: 'AWIN',
        category: COOKIE_MARKETING,
        gtmVariable: "DCCookieAwin"
    },
    {
        id: 'cookie_consent',
        name: 'Cookie-Einstellung',
        category: COOKIE_ESSENTIAL
    },
    {
        id: 'facebook_connect',
        name: 'Facebook connect',
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_facebook_connect",
        runscript: true,
        gtmVariable: "DCCookieFacebookConnect"
    },
    {
        id: 'facebook_pixel',
        name: "Facebook Pixel",
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_facebook_pixel",
        runscript: true,
        gtmVariable: "DCCookieFacebookPixel"
    },
    {
        id: 'google_analytics',
        name: "Google Analytics",
        category: COOKIE_STATISTICS,
        scriptclass: "DCCookie_google_analytics",
        runscript: true,
        gtmVariable: "DCCookieGoogleAnalytics"
    },
    {
        id: 'google_maps',
        name: "Google Maps",
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_google_maps",
        scriptclass: "DCCookie_google_maps",
        runscript: true,
        externalMediaContainerClass: "DCCookie_google_maps_container"
    },
    {
        id: 'google_tag_manager',
        name: "Google Tag Manager",
        category: COOKIE_ESSENTIAL,
        scriptclass: "DCCookie_google_tag_manager",
        runscript: true
    },
    {
        id: 'instagram',
        name: "Instagram",
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_instagram"
    },
    {
        id: 'matomo',
        name: "Matomo",
        category: COOKIE_STATISTICS,
        scriptclass: "DCCookie_matomo"
    },
    {
        id: 'microsoft_bing_ads',
        name: "Microsoft Bing Ads",
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_microsoft_bing_ads",
        gtmVariable: "DCCookieBingAds"
    },
    {
        id: 'linked_in',
        name: "LinkedIn",
        category: COOKIE_MARKETING,
        iframeclass: "DCCookie_linked_in",
        scriptclass: "DCCookie_linked_in"
    },
    {
        id: 'omnisend',
        name: "Omnisend",
        category: COOKIE_STATISTICS,
        scriptclass: "DCCookie_omnisend"
    },
    {
        id: 'open_street_map',
        name: "Open Street Map",
        category: COOKIE_ESSENTIAL,
        scriptclass: "DCCookie_open_street_map",
        iframeclass: "DCCookie_open_street_map"
    },
    {
        id: 'recaptcha',
        name: "Google reCAPTCHA",
        category: COOKIE_ESSENTIAL,
        scriptclass: "DCCookie_recaptcha"
    },
    {
        id: 'spotify',
        name: 'Spotify',
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_spotify",
        scriptclass: "DCCookie_spotify"
    },
    {
        id: 'hubspot',
        name: 'Hubspot',
        category: COOKIE_EXTERNAL_MEDIA,
        gtmVariable: "DCCookie_hubspot"
    },
    {
        id: 'trusted_shops',
        name: 'Trusted Shops',
        category: COOKIE_ESSENTIAL,
        scriptclass: "DCCookie_trusted_shops"
    },
    {
        id: 'twitter',
        name: "Twitter",
        category: COOKIE_MARKETING,
        iframeclass: "DCCookie_twitter",
        scriptclass: "DCCookie_twitter"
    },
    {
        id: 'vimeo',
        name: "Vimeo",
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_vimeo"
    },
    {
        id: 'xing',
        name: "Xing",
        category: COOKIE_MARKETING,
        iframeclass: "DCCookie_xing",
        scriptclass: "DCCookie_xing"
    },
    {
        id: 'youtube',
        name: "YouTube",
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_youtube",
        externalMediaContainerClass: "DCCookie_youtube_container"
    },
    {
        id: 'google_ads',
        name: "google_ads",
        category: COOKIE_MARKETING,
        gtmVariable: "DCCookieGoogleAds"
    },
    {
        id: 'ehi_siegel',
        name: "EHI Siegel",
        category: COOKIE_ESSENTIAL,
    },
    {
        id: 'pinterest',
        name: "Pinterest",
        category: COOKIE_EXTERNAL_MEDIA,
        gtmVariable: "DCCookiePinterest"
    },
    {
        id: 'snapchat',
        name: "Snapchat",
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_snapchat",
        gtmVariable: "DCCookieSnapchat"
    },
    {
        id: 'adobe',
        name: "adobe fonts",
        category: COOKIE_ESSENTIAL,
        iframeclass: "DCCookie_adobe"
    },
    {
        id: 'squarelovin',
        name: "squarelovin",
        category: COOKIE_EXTERNAL_MEDIA,
        scriptclass: "DCCookie_squarelovin",
        runscript: true
    },
    {
        id: 'userlike',
        name: "Userlike",
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_userlike",
        gtmVariable: "DCCookieUserlike"
    },
    {
        id: 'linked_in_insight',
        name: "LinkedIn Insight",
        category: COOKIE_MARKETING,
        scriptclass: "DCCookie_linked_in_insight",
        gtmVariable: "DCCookieLinkedInInsight",
        runscript: true
    },
    {
        id: 'microsoft_webchat',
        name: "Microsoft Webchat",
        category: COOKIE_EXTERNAL_MEDIA,
        iframeclass: "DCCookie_microsoft_webchat"
    },
    {
        id: 'hotjar_tracking',
        name : "Hotjar",
        category: COOKIE_STATISTICS,
        gtmVariable: "DCCookieHotjarTracking",
    },
    {
        id: 'google_optimize',
        name: "Google Optimize",
        category: COOKIE_STATISTICS,
        gtmVariable: "DCCookieGoogleOptimize"
    },
    {
        id: 'google_floodlight',
        name: "DoubleClick Floodlight",
        category: COOKIE_STATISTICS,
        gtmVariable: "DCCookieGoogleFloodlight"
    }
];

export class CookieProviders {
    /**
     * sets the current used providers
     *
     * @param current_providers
     */
    constructor(current_providers) {
        this.current_providers = current_providers;
    }

    /**
     * gets providers from array by current providers
     *
     * @returns {*[]}
     */
    getProviders(){
        return providers.filter(provider => this.current_providers.includes( provider.id));
    }
}