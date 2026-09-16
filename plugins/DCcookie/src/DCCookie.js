import {CookieHelper} from './CookieHelper.js';
import {CookieRenderHelper} from './CookieRenderHelper.js';
import {CookieProviders} from './CookieProviders.js';

export default class DCCookie {

    /**
     * initializes the cookie banner
     *
     * @param current_providers
     * @param managedSiteCode
     * @param langCode
     * @param altImprintLink
     * @param altPrivatePolicyLink
     * @param lastReset {Date}
     * @param showRecurrent
     * @param availableLanguages
     * @param defaultLanguage
     * @returns {Promise<void>}
     */
    async init(current_providers, managedSiteCode, langCode, altImprintLink, altPrivatePolicyLink, lastReset, availableLanguages, defaultLanguage, siteCode,  showRecurrent = false, templateNo = 1, forceActive = false, enableStatistics = false) {
        this.availableLanguages = availableLanguages;
        this.defaultLanguage = defaultLanguage;
        const language = this.getLanguage(langCode);
        const localizationData = await this.getLocalizationData(language, managedSiteCode);
        if (altImprintLink[language] !== '' && typeof altImprintLink[language] !== 'undefined') {
            localizationData.general.imprint_link = altImprintLink[language];
        }
        if (altPrivatePolicyLink[language] !== '' && typeof altPrivatePolicyLink[language] !== 'undefined') {
            localizationData.general.private_polacy_link = altPrivatePolicyLink[language];
            localizationData.provider.cookie_consent.private_polacy_link = altPrivatePolicyLink[language];
        }
        const cookieProviders = new CookieProviders(current_providers);
        let Providers = cookieProviders.getProviders();
        const cookieHelper = new CookieHelper(Providers, managedSiteCode, lastReset, siteCode, enableStatistics);
        if (forceActive) cookieHelper.forceAllCookiesActive();

        const cookieRenderHelper = new CookieRenderHelper(localizationData,cookieHelper,showRecurrent, templateNo);
        await cookieRenderHelper.renderAllExternalMediaFrames();
        if(cookieHelper.getSettingsAlreadySet()){
            await cookieRenderHelper.renderRecurrentPage();
        }else if(!this.isLegalPage(localizationData)){
            await cookieRenderHelper.renderMainPage();
        }
        cookieRenderHelper.addEventListenerNavigation();
    }

    /**
     * Checks whether the current page is the imprint or the privacy policy.
     *
     * Both must stay reachable without giving consent, so the banner is not
     * shown on top of them. Nothing is consented to by this: without consent
     * every third party script stays blocked, and the external media
     * placeholders are still rendered.
     *
     * The two pages are taken from the links the banner itself points to,
     * so the list never drifts apart from the actual page structure.
     *
     * @param localizationData
     * @returns {boolean}
     */
    isLegalPage(localizationData) {
        const normalize = (path) => {
            if (!path) return '';
            try {
                return new URL(path, window.location.origin).pathname
                    .replace(/\/+$/, '')
                    .toLowerCase();
            } catch (e) {
                return '';
            }
        };

        const current = normalize(window.location.pathname);
        if (current === '') return false;

        return [
            localizationData.general.imprint_link,
            localizationData.general.private_polacy_link,
        ].map(normalize).filter(Boolean).includes(current);
    }

    /**
     * gets the user language
     *
     * @param langCode
     * @returns {string}
     */
    getLanguage(langCode = "") {
        const userLang = langCode || navigator.language || navigator.userLanguage;
        return (this.availableLanguages.includes(userLang)) ? userLang : this.defaultLanguage;
    }

    /**
     * gets the localization file for the shop and language
     *
     * @param language
     * @param managedSiteCode
     * @returns {Promise<any>}
     */
    async getLocalizationData(language, managedSiteCode) {
        const fileName = language + '_' + managedSiteCode + '.json';
        const altFileName = language + '.json';

        if(managedSiteCode && managedSiteCode.length > 0) {
            // fetch file for shop code and use default localization as fallback
            return await fetch(location.origin + '/plugins/DCcookie/localization/' + fileName).then(response => {
                if(!response.ok) {
                    return fetch(location.origin + '/plugins/DCcookie/localization/' + altFileName);
                }
                return response;
            }).then(data => {
                return data.json();
            }).catch(error => {
                throw 'Could not load localization file for language "' + language + '" and site code "' + managedSiteCode + '"';
            });
        } else {
            // request default localization, because we have no shop code
            return await fetch(location.origin + '/plugins/DCcookie/localization/' + altFileName).then(data => {
                return data.json();
            }).catch(error => {
                throw 'Could not load localization file for language "' + language + '"';
            });
        }

    }


}