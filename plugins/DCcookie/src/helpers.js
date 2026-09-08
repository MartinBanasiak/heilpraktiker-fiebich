/**
 * gets the shop code from the body node
 *
 * @returns string|*
 */
export function getSiteCode() {
    const bodyNode = document.getElementsByTagName('body')[0];
    const siteCode = bodyNode.getAttribute('data-site_code');

    if(siteCode && siteCode.length > 0) {
        return siteCode;
    }
    return '';
}

/**
 * gets the current select language code from the body node
 *
 * @returns {string}
 */
export function getLangCode() {
    const bodyNode = document.getElementsByTagName('body')[0];
    const langCode = bodyNode.getAttribute('data-lang_code');

    if(langCode && langCode.length > 0) {
        return langCode;
    }
    return '';
}