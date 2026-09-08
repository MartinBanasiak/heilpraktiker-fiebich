const mainTemplates = {
    1: `
        <div id="dcCookieHelper--bg" class="dcCookieBarBackground">
            <div id="dcCookieHelper" class="dcCookieBar">
                <div class="dcCookieBar__main">
                    <div id="dcCookieHelper--header" class="dcCookieBar__text">
                        {{text.welcome}}
                    </div>
                    <div id="dcCookieHelper--body" class="dcCookieBar__buttons">
                        <div class="dcCookieBar__button dcCookieHelper--buttonDecline">{{text.decine}}</div>
                        <div class="dcCookieBar__button dcCookieBar__buttonaction dcCookieHelper--buttonAccept">{{text.accept}}</div>
                        <div class="dcCookieBar__button dcCookieHelper--buttonMore">{{text.more_information}}</div>
                    </div>
                </div>
                <div class="cookie-rechts-1 dcCookieBar__links">
                    <a href="{{text.private_polacy_link}}" target="_blank">{{text.private_polacy}}</a>
                    <a href="{{text.imprint_link}}" target="_blank">{{text.imprint}}</a>
                </div>
            </div>
        </div>
        `,
    2: `
        <div id="dcCookieHelper--bg" class="dcCookieBarBackground">
            <div id="dcCookieHelper" class="dcCookieBar">
                <div class="dcCookieBar__main">
                    <div id="dcCookieHelper--header" class="dcCookieBar__text">
                        {{text.welcome}}
                    </div>
                    <div id="dcCookieHelper--body" class="dcCookieBar__buttons">                
                        <div class="dcCookieBar__button dcCookieHelper--buttonMore">{{text.more_information}}</div>
                        <div class="dcCookieBar__button dcCookieBar__buttonaction dcCookieHelper--buttonAccept">{{text.accept}}</div>
                    </div>
                </div>
                <div class="cookie-rechts-1 dcCookieBar__links">
                    <a href="{{text.private_polacy_link}}" target="_blank">{{text.private_polacy}}</a>
                    <a href="{{text.imprint_link}}" target="_blank">{{text.imprint}}</a>
                </div>
            </div>
        </div>
        `,
    3: `
        <div id="dcCookieHelper--bg" class="dcCookieModal dcCookieTopPadding open">
            <div class="dcCookieModal__dialog">
                <div class="dcCookieModalBody dcCookieModalBody--individual">
                    <div id="dcCookieHelper">
                        <div class="dcCookieBar__main">
                            <div id="dcCookieHelper--header" class="dcCookieBar__text">
                                {{text.welcome}}
                            </div>
                            <div id="dcCookieHelper--body" class="dcCookieBar__buttons">         
                                <div class="dcCookieBar__button dcCookieHelper--buttonDecline">{{text.decine}}</div>      
                                <div class="dcCookieBar__button dcCookieBar__buttonaction dcCookieHelper--buttonAccept">{{text.accept}}</div>                              
                                <div class="dcCookieBar__button dcCookieHelper--buttonMore">{{text.more_information}}</div>                                
                            </div>
                        </div>
                        <div class="cookie-rechts-1 dcCookieBar__center">
                            <a href="{{text.private_polacy_link}}" target="_blank">{{text.private_polacy}}</a>
                            <a href="{{text.imprint_link}}" target="_blank">{{text.imprint}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `,
    4: `
        <div id="dcCookieHelper--bg" class="dcCookieModal dcCookieTopPadding open">
            <div class="dcCookieModal__dialog">
                <div class="dcCookieModalBody dcCookieModalBody--individual">
                    <div id="dcCookieHelper">
                        <div class="dcCookieBar__main">
                            <div id="dcCookieHelper--header" class="dcCookieBar__text">
                                {{text.welcome}}
                            </div>
                            <div id="dcCookieHelper--body" class="dcCookieBar__buttons">                                       
                                <div class="dcCookieBar__button dcCookieHelper--buttonMore">{{text.more_information}}</div>
                                <div class="dcCookieBar__button dcCookieBar__buttonaction dcCookieHelper--buttonAccept">{{text.accept}}</div>
                            </div>
                        </div>
                        <div class="cookie-rechts-1 dcCookieBar__center">
                            <a href="{{text.private_polacy_link}}" target="_blank">{{text.private_polacy}}</a>
                            <a href="{{text.imprint_link}}" target="_blank">{{text.imprint}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `
};

const providerTemplate = `
<div class="dcCookieModal open" id="dcCookieModalProvider">
    <div class="dcCookieModal__dialog">
        <div class="dcCookieModalBody dcCookieModalBody--individual">
            <div class="dcCookieModalBody__headline">{{text.provider_group_headline}}</div>
            <div class="dcCookieModalBody__info">{{text.provider_group}}</div>
            <a class="dcCookieBar__button dcCookieBar__buttonaction" id="saveAndClose">
                {{text.save_and_close}}
            </a>
            <div class="dcCookieBar__links">
                <a id="changeToWelcomeModal">
                    {{text.back}}
                </a>
                <a id="acceptOnlyEssential">
                    {{text.allow_essential_text}}
                </a>
            </div>
            <div class="dcCookieBarBoxes">
                {{ProviderInformations}}
            </div>
            <div class="cookie-rechts-1 dcCookieBar__links">
                <a href="{{text.private_polacy_link}}" target="_blank">{{text.private_polacy}}</a>
                <a href="{{text.imprint_link}}" target="_blank">{{text.imprint}}</a>
            </div>
        </div>
    </div>
</div>
`;

const recurringTemplate = `
<div id="dcCookieHelper--recurringArea" class="dcCookieBarRecurring">
    <div id="dcCookieHelper--recurringChanger" class="dcCookieBarRecurringBox hidden">
        <div class="dcCookieBarRecurringBox__head">
            <div class="dcCookieBarRecurringBox__title">{{text.provider_group_text}}</div>
            <div id="dcCookieHelper--recurringButtonClose" class="dcCookieBarRecurringBox__close">
                <svg xmlns="http://www.w3.org/2000/svg" width="14px" height="14px" viewBox="0 0 2834.65 2834.65">
                    <path fill="#000000" d="M1718.5,1417.32,2816.93,2515.75q17.73,17.72,17.72,48.72t-17.72,48.72l-203.74,203.74q-17.73,17.73-48.72,17.72t-48.72-17.72L1417.32,1718.5,318.9,2816.93q-17.73,17.73-48.72,17.72t-48.72-17.72L17.72,2613.19Q0,2595.46,0,2564.47t17.72-48.72L1116.14,1417.32,17.72,318.9Q0,301.17,0,270.18t17.72-48.72L221.46,17.72Q239.18,0,270.18,0T318.9,17.72L1417.32,1116.14,2515.75,17.72Q2533.47,0,2564.47,0t48.72,17.72l203.74,203.74q17.73,17.72,17.72,48.72t-17.72,48.72l-186,186Z"/>
                </svg>
            </div>
        </div>
        <div class="dcCookieBarRecurringBox__body">
            <div class="dcCookieBarTabs">
                <div class="dcCookieBarTabs__header">
                    <div id="dcCookieBarSettings" class="dcCookieBarTab active">{{text.cookie_settings}}</div>
                    <div id="dcCookieBarHistory" class="dcCookieBarTab">{{text.history}}</div>
                </div>
                <div class="dcCookieBarTabs__contents">
                    <div id="dcCookieBarSettingsBody" class="dcCookieBarTabContent active">
                        <div class="dcCookieBarRecurringTable">
                            <div class="dcCookieBarRecurringTable__row">
                                <div id="dcCookieHelper--recurringAll" class="dcCookieBarRecurringTable__label">
                                    {{text.take_all}}
                                </div>
                                <label class="dcCookieBarSwitch dcCookieBarAll dcCookieRecurringSwitch" for="recurringAll">
                                    <input id="recurringAll" type="checkbox" {{AllProviderSettingsSet}}>
                                    <span></span>
                                </label>
                            </div>
                                {{ProviderSettings}}
                        </div>
                    </div>
                    <div id="dcCookieBarHistoryBody" class="dcCookieBarTabContent">
                        <div class="dcCookieBarHistoryTable">
                            {{ProviderHistory}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dcCookieBarRecurringBox__footer">
            <div id="dcCookieHelper--recurringButtonSave" class="dcCookieBar__button dcCookieBar__buttonaction">{{text.save}}</div>
        </div>
    </div>
    <div id="dcCookieHelper--recurringButtonOpen" class="dcCookieBarRecurringButton">
            <span class="openlabel">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 2834.65 2834.65">
                    <path fill="#ffffff" d="M2657.48,2568.9q0,110.71-77.51,188.24t-188.24,77.51H442.92q-110.76,0-188.24-77.51T177.17,2568.9V1328.74q0-110.74,77.51-188.24T442.92,1063H620.08V797.24q0-215.93,108-398.62T1018.7,108Q1201.4,0,1417.32,0t398.62,108q182.7,108,290.66,293.43t108,401.39V1063h177.17q110.71,0,188.24,77.51t77.51,188.24ZM442.92,1328.74V2568.9H2391.73V1328.74ZM1948.82,1063V797.24q0-221.44-155-376.47t-376.48-155q-221.46,0-376.47,155t-155,376.47V1063ZM1306.59,2236.71q-44.31-44.31-44.29-110.72V1771.66q0-66.44,44.29-110.73t110.73-44.29q66.43,0,110.73,44.29t44.29,110.73V2126q0,66.44-44.29,110.72T1417.32,2281Q1350.89,2281,1306.59,2236.71Z"/>
                </svg>
            </span>
        <span class="closelabel">
                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 2834.65 2834.65">
                    <path fill="#ffffff" d="M1718.5,1417.32,2816.93,2515.75q17.73,17.72,17.72,48.72t-17.72,48.72l-203.74,203.74q-17.73,17.73-48.72,17.72t-48.72-17.72L1417.32,1718.5,318.9,2816.93q-17.73,17.73-48.72,17.72t-48.72-17.72L17.72,2613.19Q0,2595.46,0,2564.47t17.72-48.72L1116.14,1417.32,17.72,318.9Q0,301.17,0,270.18t17.72-48.72L221.46,17.72Q239.18,0,270.18,0T318.9,17.72L1417.32,1116.14,2515.75,17.72Q2533.47,0,2564.47,0t48.72,17.72l203.74,203.74q17.73,17.72,17.72,48.72t-17.72,48.72l-186,186Z"/>
                </svg>
            </span>
    </div>
</div>
`;

const externalInfoTemplate = `
    <div class="dcCookieExternal__info">
        <div class="dcCookieExternal__headline">
            {{text.external_info_headline}}
        </div>
        <div class="dcCookieExternal__text">
            {{text.external_info_text}}
        </div>
        <div class="dcCookieHelper--buttonExternalMoreInfo dcCookieBar__button">
            {{text.more_information}}    
        </div>
        <div class="dcCookieHelper--buttonExternalAccept dcCookieBar__button dcCookieBar__buttonaction">
            {{text.external_accept}}    
        </div>
    </div>
`;

export class CookieRenderHelper {
    /**
     *
     * @param localizationData
     * @param cookieHelper
     * @param showRecurrent
     */
    constructor(localizationData, cookieHelper, showRecurrent, templateNo = 1) {
        this.localizationData = localizationData;
        this.cookieHelper = cookieHelper;
        this.showRecurrent = showRecurrent;
        this.mainTemplateHTML = '';
        this.templateNo = templateNo;
    }

    /**
     * renders main banner template and adds event listener
     *
     * @returns {Promise<void>}
     */
    async renderMainPage(){
        let TemplateHTML = await this.renderMainTemplate(mainTemplates[this.templateNo]);
        if(TemplateHTML !== null) {
            //document.body.innerHTML += TemplateHTML;
            var newElement = document.createElement('div');
            newElement.innerHTML = TemplateHTML;
            document.body.appendChild(newElement);
        }
        this.cookieHelper.cookieStatisticAction("ACTION_SHOW_BANNER");
        this.addEventListenerMain();
    }

    /**
     * renders providers settings page and adds event listener
     *
     * @returns {Promise<void>}
     */
    async renderProviderPage(){
        let TemplateHTML = await this.renderMainTemplate(providerTemplate);
        if(TemplateHTML !== null) {
            //document.body.innerHTML += TemplateHTML;
            var newElement = document.createElement('div');
            newElement.innerHTML = TemplateHTML;
            document.body.appendChild(newElement);
        }
        this.addEventListenerProvider();
    }

    /**
     * renders button for opening history and cookie settings in small version
     *
     * @returns {Promise<void>}
     */
    async renderRecurrentPage() {
        if (this.showRecurrent) {
            let recurringElement = document.getElementById('dcCookieHelper--recurringArea');
            if (recurringElement) recurringElement.remove();
            let TemplateHTML = await this.renderMainTemplate(recurringTemplate);
            if (TemplateHTML !== null) {
                //document.body.innerHTML += TemplateHTML;
                var newElement = document.createElement('div');
                newElement.innerHTML = TemplateHTML;
                document.body.appendChild(newElement);
            }
            this.addEventListenerRecurring();
        }
        hide(document.getElementById('dcCookieHelper--bg'));
    }

    /**
     * renders sub pages for the recurrent page
     *
     * @param TemplateText
     * @returns {Promise<null|*>}
     */
    async renderMainTemplate(TemplateText) {
        if(TemplateText == null) {
            return null;
        }
        return TemplateText.replace(/\{\{[^{]+\}\}/g, (match) => {
            const variableName = match.replace('{{','').replace('}}','');
            if(variableName.startsWith('text.')) {
                const requestedTextKey = variableName.replace('text.','');
                const requestedText = this.localizationData['general'][requestedTextKey];
                return (requestedText !== undefined) ? requestedText : '';
            }
            if(variableName.startsWith('AllProviderSettingsSet')) {
                let requestedText = 'checked="checked"';
                this.cookieHelper.providers.forEach(function (Provider) {
                    if(!Provider.state) requestedText = '';
                });
                return requestedText;
            }
            if(variableName.startsWith('ProviderInformations')) {
                let requestedText = '';
                requestedText += this.buildInformation('essential');
                requestedText += this.buildInformation('statistics');
                requestedText += this.buildInformation('external_media');
                requestedText += this.buildInformation('marketing');
                return requestedText;
            }
            if(variableName.startsWith('ProviderSettings')) {
                let requestedText = '';
                requestedText += this.buildCollapse('essential');
                requestedText += this.buildCollapse('statistics');
                requestedText += this.buildCollapse('external_media');
                requestedText += this.buildCollapse('marketing');
                return requestedText;
            }
            if(variableName.startsWith('ProviderHistory')) {
                let requestedText = '';
                this.cookieHelper.dcCookieHistory.forEach(function (dcCookieHistoryEntry) {
                    requestedText = '<div class="dcCookieBarHistoryTable__row">' +
                        '<div class="dcCookieBarHistoryTable__badge '+(dcCookieHistoryEntry.state ? 'active' : '')+'"></div>' +
                        '<div class="dcCookieBarHistoryTable__label">' +
                        this.localizationData['provider'][dcCookieHistoryEntry.dcCookieId]['name']+
                        '</div>'+
                        '<div class="dcCookieBarHistoryTable__date">' +
                        convertDate(dcCookieHistoryEntry.date)+
                        '</div>' +
                        ' <div class="dcCookieBarHistoryTable__active">' +
                        (dcCookieHistoryEntry.state ? this.localizationData['general']['activate'] : this.localizationData['general']['deactivate'])  +
                        '</div>' +
                        '</div>'+requestedText;
                }.bind(this));
                return (requestedText !== '') ? requestedText : '';
            }
        });
    }

    async renderAllExternalMediaFrames()
    {
        const declinedProviders = this.cookieHelper.getAllDeclinedExternalMediaProviders();

        for(let i = 0; i < declinedProviders.length; i++) {
            let provider = declinedProviders[i];
            if (!provider.externalMediaContainerClass) continue;


            let templateHTML = this.renderExternalMediaFrameForProvider(provider);

            let containerElements = document.getElementsByClassName(provider.externalMediaContainerClass);
            for (let j = 0; j < containerElements.length; j++) {
                let containerElement = containerElements[j];
                containerElement.innerHTML = templateHTML;
            }
        }
        this.addEventListenerForExternalMediaFrames();
    }

    renderExternalMediaFrameForProvider(provider)
    {
        return externalInfoTemplate
            .replace(/\{\{[^{]+\}\}/g, (match) => {
                const variableName = match.replace('{{', '').replace('}}', '');
                if (variableName.startsWith('text.')) {
                    const requestedTextKey = variableName.replace('text.', '');
                    const requestedText = this.localizationData['general'][requestedTextKey];
                    return (requestedText !== undefined) ? requestedText : '';
                }
                if (variableName.startsWith('provider.')) {
                    const requestedTextKey = variableName.replace('provider.', '');
                    const requestedText = this.localizationData['provider'][provider.id][requestedTextKey];
                    return (requestedText !== undefined) ? requestedText : '';
                }
                return '';
            })
            .replace(/\{\{[^{]+\}\}/g, (match) => {
                const variableName = match.replace('{{', '').replace('}}', '');
                if (variableName.startsWith('text.')) {
                    const requestedTextKey = variableName.replace('text.', '');
                    const requestedText = this.localizationData['general'][requestedTextKey];
                    return (requestedText !== undefined) ? requestedText : '';
                }
                if (variableName.startsWith('provider.')) {
                    const requestedTextKey = variableName.replace('provider.', '');
                    const requestedText = this.localizationData['provider'][provider.id][requestedTextKey];
                    return (requestedText !== undefined) ? requestedText : '';
                }
                return '';
            });
    }

    addEventListenerForExternalMediaFrames()
    {
        document.addEventListener('click', function (event) {
            if (typeof event.target.classList === 'undefined' || event.target.classList === null) return;
            if (event.target.classList.contains('dcCookieHelper--buttonExternalMoreInfo')) {
                this.renderProviderPage();
            }
            if (event.target.classList.contains('dcCookieHelper--buttonExternalAccept')) {
                this.cookieHelper.toggleCookieGroup('external_media',true);
                this.renderRecurrentPage();
            }
        }.bind(this));
    }
    /**
     * fills information for given provider category name
     *
     * @param category
     * @returns {string}
     */
    buildInformation(category){
        let requestedTextEssential = '';
        let requestedBool = true;
        let provider_essential = this.cookieHelper.providers.filter(provider => provider.category == category);
        let i = 0;
        let category_state = false;
        if(category=='essential'){
            category_state = true;
        }
        provider_essential.forEach(function (Provider) {
            i++;
            requestedTextEssential += '<div class="dcCookieBarTable">\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '               <div class="dcCookieBarTable__cell dcCookieBarTable__cell--description">'+ this.localizationData['provider'][Provider.id]['name']+'</div>\n' +
                '               <div class="dcCookieBarTable__cell dcCookieBarTable__cell--switch dcCookieHelper--Lines--'+category+'--provider">\n' +
                '                <label class="dcCookieBarSwitch__label" for="recurringAll--'+Provider.id+'">' + this.localizationData['general']['accept_one'] + '</label>' +
                '            <label class="dcCookieBarSwitch dcCookieBarSwitchInfo dcCookieBarChild" for="recurringAll--'+Provider.id+'" data-categorychild="'+category+'">\n' +
                '            <input class="subCookieAcceptSlider" id="recurringAll--'+Provider.id+'" data-id="'+Provider.id+'" type="checkbox" '+(Provider.state|category_state ? 'checked="checked"' : '')+' '+(category_state ? 'disabled="disabled"' : '')+'>\n' +
                '            <span></span>\n' +
                '            </label>\n' +
                '               </div>\n' +
                '            </div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['name']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">'+this.localizationData['provider'][Provider.id]['name']+'</div>\n' +
                '</div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['provider']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">\n' +
                this.localizationData['provider'][Provider.id]['website_owner']+'\n' +
                '</div>\n' +
                '</div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['purpose']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">\n' +
                ''+this.localizationData['provider'][Provider.id]['text']+'\n' +
                ' </div>\n' +
                '</div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['time']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">\n' +
                ''+this.localizationData['provider'][Provider.id]['cookie_time']+'\n' +
                ' </div>\n' +
                '</div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['cookie_name']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">\n' +
                ''+this.localizationData['provider'][Provider.id]['cookie_name']+'\n' +
                ' <a href="'+ this.localizationData['general']['private_polacy_link']+'#sectionGA"\n' +
                'target="_blank">('+ this.localizationData['general']['more_information']+')</a>\n' +
                '</div>\n' +
                '</div>\n' +
                '<div class="dcCookieBarTable__row">\n' +
                '<div class="dcCookieBarTable__cell">'+ this.localizationData['general']['private_polacy']+'</div>\n' +
                '<div class="dcCookieBarTable__cell">\n' +
                '<a href="'+this.localizationData['provider'][Provider.id]['private_polacy_link']+'" target="_blank">'+this.localizationData['provider'][Provider.id]['private_polacy_link']+'</a>\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>\n';
            if(!Provider.state) requestedBool = false;
        }.bind(this));
        /*
         +
                        '' +
                        '' +
                        '' +
                        '' +
                        '<div class="dcCookieBarRecurringTable__row hidden dcCookieHelper--Lines--'+category+' hidden">' +
                        '<div id="dcCookieHelper--recurringAll--'+Provider.id+'" class="dcCookieBarRecurringTable__label">-->' +
                        this.localizationData['provider'][Provider.id]['name'] +
                        '</div>' +
                        '<label class="dcCookieBarSwitch dcCookieBarChild" for="recurringAll--'+Provider.id+'" data-categorychild="'+category+'">' +
                        '<input id="recurringAll--'+Provider.id+'" data-id="'+Provider.id+'" type="checkbox" '+(Provider.state ? 'checked="checked"' : '')+'>' +
                        '<span></span>' +
                        '</label>' +
                        '</div>'
                        */



        const requestedTextEssentialStart = '<div class="dcCookieBarBox" id="dcCookieHelper--informations-'+category+'" data-category="'+category+'">' +
            '<div class="dcCookieBarBox__head">' +
            '<div id="dcCookieHelper--providerAll'+category+'--provider" class="dcCookieBarBox__title" data-category="'+category+'">' +
            ''+ this.localizationData['general'][category]+' ('+i+')' +
            '</div>' +
            '<label class="dcCookieBarSwitch dcCookieBarSwitchInfo dcCookieBarCategory" for="providerAll--'+category+'--provider">' +
            '<input id="providerAll--'+category+'--provider" type="checkbox" '+(requestedBool|category_state ? 'checked="checked"' : '')+' '+(category_state ? 'disabled="disabled"' : '')+'>' +
            '<span></span>' +
            '</label>' +
            '</div>' +
            '<div class="dcCookieBarBox__info">'+ this.localizationData['general'][category+'_text']+'</div>' +
            '<a class="dcCookieBarShowMore" data-target="'+category+'CookieInfo">'+ this.localizationData['general']['show_cookie_information']+'</a>' +
            '<div class="dcCookieBarMoreBox hidden" id="'+category+'CookieInfo">';
        const requestedTextEssentialEnd = '' +
            '</div>\n' +
            '</div>\n';
        return (requestedTextEssential !== '') ? (requestedTextEssentialStart + requestedTextEssential + requestedTextEssentialEnd) : '';
    }

    /**
     * renders collapse information for category
     *
     * @param category
     * @returns {string}
     */
    buildCollapse(category){
        let requestedTextEssential = '';
        let requestedBool = true;
        let provider_essential = this.cookieHelper.providers.filter(provider => provider.category == category);
        let category_state = false;
        if(category=='essential'){
            category_state = true;
        }

        provider_essential.forEach(function (Provider) {
            requestedTextEssential += '<div class="dcCookieBarRecurringTable__row hidden dcCookieBarRecurringTable__row--child dcCookieHelper--Lines--'+category+'">' +
                '<div id="dcCookieHelper--recurringAll--'+Provider.id+'" class="dcCookieBarRecurringTable__label">' +
                this.localizationData['provider'][Provider.id]['name'] +
                '</div>' +
                '<label class="dcCookieBarSwitch dcCookieBarChild dcCookieRecurringSwitch" for="recurringAll--'+Provider.id+'--provider" data-categorychild="'+category+'">' +
                '<input id="recurringAll--'+Provider.id+'--provider" data-id="'+Provider.id+'" type="checkbox" '+(Provider.state|category_state ? 'checked="checked"' : '')+' '+(category_state ? 'disabled="disabled"' : '')+'>' +
                '<span></span>' +
                '</label>' +
                '</div>';
            if(!Provider.state) requestedBool = false;
        }.bind(this));
        const requestedTextEssentialStart = '<div class="dcCookieBarRecurringTable__row dcCookieBarRecurringTable__row--parent">' +
            '<div id="dcCookieHelper--recurringAll'+category+'" class="dcCookieBarRecurringTable__label dcCookieHelper--Header" data-category="'+category+'">' +
            this.localizationData['general'][category] +
            '</div>' +
            '<label class="dcCookieBarSwitch dcCookieBarCategory dcCookieRecurringSwitch" for="recurringAll--'+category+'">' +
            '<input id="recurringAll--'+category+'" type="checkbox" '+(requestedBool|category_state ? 'checked="checked"' : '')+' '+(category_state ? 'disabled="disabled"' : '')+'>' +
            '<span></span>' +
            '</label>' +
            '</div>';
        return (requestedTextEssential !== '') ? (requestedTextEssentialStart + requestedTextEssential) : '';
    }

    /**
     * adds several event listener to the providers
     */
    addEventListenerProvider(){
        var userSelection = document.getElementsByClassName('dcCookieBarShowMore');
        for(let i = 0; i < userSelection.length; i++) {
            (function(index) {
                userSelection[index].addEventListener("click", function() {
                    toogle(document.getElementById(userSelection[index].dataset.target));
                });
            })(i);
        }
        let dcCookieBarSwitch = document.getElementsByClassName('dcCookieBarSwitchInfo');
        for (let i = 0; i < dcCookieBarSwitch.length; i++){
            if( dcCookieBarSwitch[i].classList.contains( 'dcCookieBarCategory')){
                dcCookieBarSwitch[i].addEventListener('click', function(elem) {
                    let dcCookieHelperLines = document.getElementsByClassName('dcCookieHelper--Lines--'+dcCookieBarSwitch[i].parentElement.firstElementChild.dataset.category+'--provider');
                    for (let j = 0; j < dcCookieHelperLines.length; j++) {
                        dcCookieHelperLines[j].children[1].children[0].checked = dcCookieBarSwitch[i].childNodes[0].checked;
                    }
                }.bind(this));
            }
            if( dcCookieBarSwitch[i].classList.contains( 'dcCookieBarChild')){
                dcCookieBarSwitch[i].addEventListener('click', function(elem) {
                    elem.stopPropagation();
                    let dcCookieHelperLines = document.getElementsByClassName('dcCookieHelper--Lines--'+dcCookieBarSwitch[i].dataset.categorychild+'--provider');
                    let dcCookieHelperLinesTrue = true;
                    for (let j = 0; j < dcCookieHelperLines.length; j++) {
                        if(!dcCookieHelperLines[j].children[1].children[0].checked) dcCookieHelperLinesTrue = false;
                    }
                    if(dcCookieHelperLinesTrue) document.getElementById('providerAll--'+dcCookieBarSwitch[i].dataset.categorychild+'--provider').checked = true;
                    else document.getElementById('providerAll--'+dcCookieBarSwitch[i].dataset.categorychild+'--provider').checked = false;
                }.bind(this));
            }
        }
        document.getElementById('changeToWelcomeModal').addEventListener('click', function() {
            document.getElementById('dcCookieModalProvider').parentElement.remove();
            // this.addEventListenerMain();
        }.bind(this));
        document.getElementById('acceptOnlyEssential').addEventListener('click', function() {
            document.getElementById('dcCookieModalProvider').remove();
            this.cookieHelper.toggleCookieGroup('essential',true);
            this.cookieHelper.toggleCookieGroup('statistics', false);
            this.cookieHelper.toggleCookieGroup('external_media', false);
            this.cookieHelper.toggleCookieGroup('marketing', false);
            this.cookieHelper.cookieStatisticAction("ACTION_ONLY_ESSENTIAL");
            this.renderRecurrentPage();
        }.bind(this));

        document.getElementById('saveAndClose').addEventListener('click', function() {
            let dcCookieBarSwitch = document.getElementsByClassName('dcCookieBarSwitch');
            for (let i = 0; i < dcCookieBarSwitch.length; i++){
                if( dcCookieBarSwitch[i].classList.contains( 'dcCookieBarChild')){
                    this.cookieHelper.toggleCookie(dcCookieBarSwitch[i].children[0].dataset.id,dcCookieBarSwitch[i].children[0].checked);
                }
            }
            document.getElementById('dcCookieModalProvider').remove();
            this.cookieHelper.cookieStatisticAction("ACTION_SAVE_CUSTOM");
            this.renderRecurrentPage();
        }.bind(this));
    }

    /**
     * adds event listener for the cookie banner
     */
    addEventListenerMain(){
        let declineButtons = document.getElementsByClassName('dcCookieHelper--buttonDecline');

        for(let i = 0; i < declineButtons.length; i++) {
            declineButtons[i].addEventListener('click', function () {
                this.cookieHelper.toggleCookieGroup('essential', true);
                this.cookieHelper.toggleCookieGroup('statistics', false);
                this.cookieHelper.toggleCookieGroup('external_media', false);
                this.cookieHelper.toggleCookieGroup('marketing', false);
                this.cookieHelper.cookieStatisticAction("ACTION_DECLINE_ALL");
                this.renderRecurrentPage();
            }.bind(this));
        }
        let moreButtons = document.getElementsByClassName('dcCookieHelper--buttonMore')
        for(let i = 0; i < moreButtons.length; i++) {
            moreButtons[i].addEventListener('click', function () {
                this.cookieHelper.cookieStatisticAction("ACTION_MORE_INFORMATION");
                this.renderProviderPage();
                // this.addEventListenerMain();
            }.bind(this));
        }
        let acceptButtons = document.getElementsByClassName('dcCookieHelper--buttonAccept');
        for(let i = 0; i < acceptButtons.length; i++) {
            acceptButtons[i].addEventListener('click', function() {
                this.cookieHelper.toggleCookieGroup('essential',true);
                this.cookieHelper.toggleCookieGroup('statistics',true);
                this.cookieHelper.toggleCookieGroup('external_media',true);
                this.cookieHelper.toggleCookieGroup('marketing',true);
                this.cookieHelper.cookieStatisticAction("ACTION_ACCEPT_ALL");
                this.renderRecurrentPage();
            }.bind(this));
        }
    }

    /**
     * adds event listener for the cookie modal buttons
     */
    addEventListenerRecurring(){
        document.getElementById('dcCookieBarHistory').addEventListener('click', function() {
            document.getElementById('dcCookieBarHistory').classList.add('active');
            document.getElementById('dcCookieBarSettings').classList.remove('active');
            document.getElementById('dcCookieBarHistoryBody').classList.add('active');
            document.getElementById('dcCookieBarSettingsBody').classList.remove('active');
            let requestedText = '';
            this.cookieHelper.dcCookieHistory.forEach(function (dcCookieHistoryEntry) {
                requestedText = '<div class="dcCookieBarHistoryTable__row">' +
                    '<div class="dcCookieBarHistoryTable__badge '+(dcCookieHistoryEntry.state ? 'active' : '')+'"></div>' +
                    '<div class="dcCookieBarHistoryTable__label">' +
                    this.localizationData['provider'][dcCookieHistoryEntry.dcCookieId]['name']+
                    '</div>'+
                    '<div class="dcCookieBarHistoryTable__date">' +
                    convertDate(dcCookieHistoryEntry.date)+
                    '</div>' +
                    ' <div class="dcCookieBarHistoryTable__active">' +
                    (dcCookieHistoryEntry.state ? this.localizationData['general']['activate'] : this.localizationData['general']['deactivate'])  +
                    '</div>' +
                    '</div>'+requestedText;
            }.bind(this));
            document.getElementsByClassName('dcCookieBarHistoryTable')[0].innerHTML = requestedText;
        }.bind(this));
        document.getElementById('dcCookieBarSettings').addEventListener('click', function() {
            document.getElementById('dcCookieBarHistory').classList.remove('active');
            document.getElementById('dcCookieBarSettings').classList.add('active');
            document.getElementById('dcCookieBarHistoryBody').classList.remove('active');
            document.getElementById('dcCookieBarSettingsBody').classList.add('active');
        }.bind(this));
        document.getElementById('dcCookieHelper--recurringButtonOpen').addEventListener('click', function() {
            toogle(document.getElementById('dcCookieHelper--recurringChanger'));
        }.bind(this));
        document.getElementById('dcCookieHelper--recurringButtonClose').addEventListener('click', function() {
            toogle(document.getElementById('dcCookieHelper--recurringChanger'));
        }.bind(this));
        let dcCookieHelperHeader = document.getElementsByClassName('dcCookieHelper--Header');
        for (let i = 0; i < dcCookieHelperHeader.length; i++){
            dcCookieHelperHeader[i].addEventListener('click', function() {
                toogleactive(dcCookieHelperHeader[i]);
                let dcCookieHelperLines = document.getElementsByClassName(' dcCookieHelper--Lines--'+dcCookieHelperHeader[i].dataset.category);
                for (let i = 0; i < dcCookieHelperLines.length; i++)  toogle(dcCookieHelperLines[i]);
            }.bind(this));
        }
        let dcCookieBarSwitch = document.getElementsByClassName('dcCookieBarSwitch');
        for (let i = 0; i < dcCookieBarSwitch.length; i++){
            if (dcCookieBarSwitch[i].classList.contains('dcCookieRecurringSwitch')) {
                if (dcCookieBarSwitch[i].classList.contains('dcCookieBarAll')) {
                    dcCookieBarSwitch[i].addEventListener('click', function (elem) {
                        let dcCookieHelperLines = document.getElementsByClassName('dcCookieBarSwitch');
                        for (let j = 0; j < dcCookieHelperLines.length; j++) {
                            if (!dcCookieHelperLines[j].childNodes[0].disabled) {
                                dcCookieHelperLines[j].childNodes[0].checked = dcCookieBarSwitch[i].childNodes[1].checked;
                            }
                        }
                    }.bind(this));
                }
                if (dcCookieBarSwitch[i].classList.contains('dcCookieBarCategory')) {
                    dcCookieBarSwitch[i].addEventListener('click', function (elem) {
                        let dcCookieHelperLines = document.getElementsByClassName('dcCookieHelper--Lines--' + dcCookieBarSwitch[i].parentElement.firstElementChild.dataset.category);
                        for (let j = 0; j < dcCookieHelperLines.length; j++) {
                            dcCookieHelperLines[j].childNodes[1].childNodes[0].checked = dcCookieBarSwitch[i].childNodes[0].checked;
                        }

                        let dcCookieHelperLinesForAll = document.getElementsByClassName('dcCookieBarChild');
                        let dcCookieHelperLinesTrueForAll = true;
                        for (let j = 0; j < dcCookieHelperLinesForAll.length; j++) {
                            if (!dcCookieHelperLinesForAll[j].childNodes[0].checked) dcCookieHelperLinesTrueForAll = false;
                        }
                        if (dcCookieHelperLinesTrueForAll) document.getElementById('dcCookieHelper--recurringAll').nextElementSibling.firstElementChild.checked = true;
                        else document.getElementById('dcCookieHelper--recurringAll').nextElementSibling.firstElementChild.checked = false;
                    }.bind(this));
                }
                if (dcCookieBarSwitch[i].classList.contains('dcCookieBarChild')) {
                    dcCookieBarSwitch[i].addEventListener('click', function (elem) {
                        let dcCookieHelperLines = document.getElementsByClassName('dcCookieHelper--Lines--' + dcCookieBarSwitch[i].dataset.categorychild);
                        let dcCookieHelperLinesTrue = true;
                        for (let j = 0; j < dcCookieHelperLines.length; j++) {
                            if (!dcCookieHelperLines[j].childNodes[1].firstElementChild.checked) dcCookieHelperLinesTrue = false;
                        }
                        if (dcCookieHelperLinesTrue) document.getElementById('dcCookieHelper--recurringAll' + dcCookieBarSwitch[i].dataset.categorychild).nextElementSibling.firstChild.checked = true;
                        else document.getElementById('dcCookieHelper--recurringAll' + dcCookieBarSwitch[i].dataset.categorychild).nextElementSibling.firstChild.checked = false;

                        let dcCookieHelperLinesForAll = document.getElementsByClassName('dcCookieBarChild');
                        let dcCookieHelperLinesTrueForAll = true;
                        for (let j = 0; j < dcCookieHelperLinesForAll.length; j++) {
                            if (!dcCookieHelperLinesForAll[j].childNodes[0].checked) dcCookieHelperLinesTrueForAll = false;
                        }
                        if (dcCookieHelperLinesTrueForAll) document.getElementById('dcCookieHelper--recurringAll').nextElementSibling.firstElementChild.checked = true;
                        else document.getElementById('dcCookieHelper--recurringAll').nextElementSibling.firstElementChild.checked = false;
                    }.bind(this));
                }
            }
        }
        document.getElementById('dcCookieHelper--recurringButtonSave').addEventListener('click', function() {
            /* ToDo: Set Cookies */
            let dcCookieBarSwitch = document.getElementsByClassName('dcCookieBarSwitch');
            for (let i = 0; i < dcCookieBarSwitch.length; i++){
                if( dcCookieBarSwitch[i].classList.contains( 'dcCookieBarChild')){
                    this.cookieHelper.toggleCookie(dcCookieBarSwitch[i].childNodes[0].dataset.id,dcCookieBarSwitch[i].childNodes[0].checked);
                }
            }
            toogle(document.getElementById('dcCookieHelper--recurringChanger'));
        }.bind(this));

    }

    addEventListenerNavigation() {
        document.addEventListener('click', function (event) {
            if (typeof event.target.classList === 'undefined' || event.target.classList === null) return;
            if (event.target.classList.contains('dcCookieBar__Navigation_Button')) {
                this.renderProviderPage();
            }
        }.bind(this));
    }
}


let dcCookieHelperHeader = document.getElementsByClassName('dcCookieHelper--Header');
for (let i = 0; i < dcCookieHelperHeader.length; i++){
    dcCookieHelperHeader[i].addEventListener('click', function() {
        let dcCookieHelperLines = document.getElementsByClassName(' dcCookieHelper--Lines--'+dcCookieHelperHeader[i].dataset.category);
        for (let i = 0; i < dcCookieHelperLines.length; i++)  toogle(dcCookieHelperLines[i]);
    });
}

/**
 * toggles the class hidden on the element
 *
 * @param elem
 */
var toogle = function (elem) {
    if(elem !== null){
        elem.classList.toggle('hidden');
    }
};

var hide = function (elem) {
    if (elem !== null){
        elem.classList.add('hidden');
    }
}

var show = function (elem) {
    if (elem !== null){
        elem.classList.remove('hidden');
    }
}

/**
 * toggles the class active on the element
 *
 * @param elem
 */
var toogleactive = function (elem) {
    if(elem !== null){
        elem.classList.toggle('active');
    }
};

/**
 * converts the date in a specific format
 *
 * @param date_string
 * @returns {string}
 */
function convertDate(date_string) {
    let date = new Date(date_string);
    let yyyy = date.getFullYear().toString();
    let mm = (date.getMonth()+1).toString();
    let dd  = date.getDate().toString();
    let hh  = date.getHours().toString();
    let mi  = date.getMinutes().toString();

    let mmChars = mm.split('');
    let ddChars = dd.split('');
    let hhChars = hh.split('');
    let miChars = mi.split('');

    return (ddChars[1]?dd:"0"+ddChars[0]) + '.' + (mmChars[1]?mm:"0"+mmChars[0]) + '.' + yyyy + ' '+ (hhChars[1]?hh:"0"+hhChars[0]) +':'+ (miChars[1]?mi:"0"+miChars[0]);
}