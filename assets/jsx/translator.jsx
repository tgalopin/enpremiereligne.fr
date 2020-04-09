import fr_FR from './translations/fr_FR';

const translations = {
    fr_FR: fr_FR,
};

export function trans(key) {
    const locale = document.body.getAttribute('data-locale');

    return translations[locale][key] ?? key;
}
