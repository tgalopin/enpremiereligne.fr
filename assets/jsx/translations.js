import i18n from "i18next";
import { initReactI18next } from "react-i18next";
i18next
    .use(initReactI18next)
    .init({
        resources: {
            en: {
                shopping: {
                    title: 'I need help with my shopping'
                },
                post: {
                    title: 'Post title'
                }
            },
            fr: {
                shopping: {
                    title: 'J\'ai besoin d\'aide pour effectuer mes courses (foo)'
                },
                post: {
                    title: 'Titre d\'article'
                }
            }
        }
    });