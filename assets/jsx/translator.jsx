import fr_FR from './translations/fr_FR';
import en_NZ from './translations/en_NZ';

const translations = {
    fr_FR: fr_FR,
    en_NZ: en_NZ,
};

export function trans(key) {
    const locale = document.body.getAttribute('data-locale');

    return translations[locale][key] ?? key;
}
