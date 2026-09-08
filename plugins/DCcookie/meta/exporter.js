const fs = require('fs');

const basePath = './../localization/'
const languages = ['de', 'en', 'fr', 'nl'];
const skipKeys = [
    'imprint_link',
    'private_polacy_link'
];


let data = [];

let csvData = "";
languages.forEach((language) => {
    let raw = fs.readFileSync(basePath + language + '.json');
    data[language] = JSON.parse(raw);
    csvData += ";" + language;
});
csvData += "\n";

for (const [key, value] of Object.entries(data[languages[0]].general)) {
    if (skipKeys.includes(key)) continue;

    let line = "general-" + key;
    languages.forEach((language) => {
       let value = data[language]['general'][key].replace('amp;', '');
       line += ";" +  value;
    });
    if (key == "back") console.log(line);

    csvData = csvData + line + "\n";
}

for (const [key, value] of Object.entries(data[languages[0]].provider)) {
    let line = "provider-" + key + "-text";
    languages.forEach((language) => {
        let value = data[language]['provider'][key].text.replace('amp;', '');
        line += ";" + value;
    });

    csvData = csvData + line + "\n";
}
fs.writeFile("translate.csv", csvData, 'utf8', (err) => {
    console.log(err);
});
