const fs = require('fs');
const parse = require('./../node_modules/csv-parse')

const importFileName = "translate.csv";
const basePathTranslations = "./../localization/";

var data = [];

fs.createReadStream(importFileName, "utf8")
    .pipe(parse({delimiter: ';'}))
    .on('data', (r) => {
        data.push(r);
    })
    .on('end', () => {
        const languages = data[0];
        let existingData = [];
        languages.forEach((language) => {
            if (language.trim() == "" ) return;
            existingData[language] = readExistingFile(language);
        });

        data.forEach((line) => {
            let key = line[0];
            let keys = key.split("-");

            for(let i = 2; i < line.length; i++) {
                let languageKey = languages[i];
                let value = line[i];
                switch(keys.length) {
                    case 1:
                        existingData[languageKey][keys[0]] = value.replace('&', '&amp;');
                        break;
                    case 2:
                        existingData[languageKey][keys[0]][keys[1]] = value.replace('&', '&amp;');
                        break;
                    case 3:
                        existingData[languageKey][keys[0]][keys[1]][keys[2]] = value.replace('&', '&amp;');
                        break;
                    case 4:
                        existingData[languageKey][keys[0]][keys[1]][keys[2]][keys[3]] = value.replace('&', '&amp;');
                        break;
                    case 5:
                        existingData[languageKey][keys[0]][keys[1]][keys[2]][keys[3]][keys[4]] = value.replace('&', '&amp;');
                        break;
                }
            }
        });
        writeNewData(existingData);
    });

function readExistingFile(languageCode)
{
    const raw = fs.readFileSync(basePathTranslations + languageCode + '.json', "utf8");
    const jsonData = JSON.parse(raw);
    return jsonData;
}

function writeNewData(data) {
    for (const [language, content] of Object.entries(data)) {
        fs.writeFileSync(basePathTranslations + language + ".json", JSON.stringify(content), "utf8", (err) => {
            console.log(err);
        });
    }
}

