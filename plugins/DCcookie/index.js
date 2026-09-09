import DCCookie from "./src/DCCookie.js";
import {getLangCode, getSiteCode} from "./src/helpers";

let siteCode = getSiteCode();
let langCode = getLangCode();

/**
 * Controls since when the last cookie settings apply
 * If a users last settings are before this date he needs to re-fill the cookie settings
 * (aka set to current date if you add a consent to reset it for all users)
 * @type {Date}
 */
let lastReset = new Date('2021-03-05T12:00:00');
/**
 * Controls the visibility of the lock in the bottom left corner
 * @type {boolean}
 */
let showRecurrent = false;

/**
 * Controls the visible main Template:
 * 1: Default template
 * 2: Default template without decline button
 * 3: Modal template
 * 4: Modal template without decline button
 * @type {number}
 */

let templateNo = 4;

/**
 * Choose if statistic collection is enabled
 * @type {boolean}
 */
let enableStatistics = false;

/**
 * language settings
 */
let availableLanguages = ['de', 'en'];
let defaultLanguage = 'de';

/**
 * session storage identifier (for different managers for one projekt e.g. multiple sites with different domains, etc.)
 * looks for custom translation file if exists (e.g. de_IDENTIFIERCODE.json)
 * @type {string}
 */
let identifierCode = '';

/**
 * If set to true, the banner won't be shown and set everything to active by default! USE WITH CARE!
 * @type {boolean}
 */
let forceActive = false;

/**
 * Use an alternative imprint link while keeping the same identifier context (good for login sites)
 * Mapped by language
 * @type {{}}
 */
let altImprintLink = {};
/**
 * Use an alternative private policy link while keeping the consent identifier context (good for login sites)
 * Mapped by language
 * @type {{}}
 */
let altPrivatePolicyLink = {};

/**
 * providers which are currently used
 *
 * @type {*[]}
 */
let providers = [
    "cookie_consent",
    "google_tag_manager",
    "google_analytics",
    "google_maps",
    "trustindex"
];
altPrivatePolicyLink.de = '/de/info/datenschutz/';
altImprintLink.de = '/de/info/impressum/';
if (siteCode === 'b2b') {
    forceActive = true;
}
const dcCookie = new DCCookie();
dcCookie.init(providers, identifierCode, langCode, altImprintLink, altPrivatePolicyLink, lastReset, availableLanguages, defaultLanguage, siteCode, showRecurrent, templateNo, forceActive, enableStatistics);
