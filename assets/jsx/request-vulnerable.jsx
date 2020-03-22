const replacements = [
    {
        id: 'field-firstname',
        self: 'Votre prénom (requis)',
        other: 'Prénom de la personne concernée (requis)',
    },
    {
        id: 'field-lastname',
        self: 'Votre nom (requis)',
        other: 'Nom de la personne concernée (requis)',
    },
    {
        id: 'field-zipcode',
        self: 'Votre code postal (français uniquement, requis)',
        other: 'Le code postal de la personne concernée (français uniquement, requis)',
    },
    {
        id: 'field-email',
        self: 'Votre adresse e-mail (requis)',
        other: 'Adresse e-mail de la personne concernée (requis)',
    },
];

const typeCheckbox = document.getElementById('vulnerable_help_request_isCloseOne');
const fieldCc = document.getElementById('field-other');

function refresh() {
    const checked = typeCheckbox.checked;

    if (checked) {
        fieldCc.style.display = 'block';
        fieldCc.querySelectorAll('input').forEach((input) => {
            input.required = true;
        });
    } else {
        fieldCc.style.display = 'none';
        fieldCc.querySelectorAll('input').forEach((input) => {
            input.required = false;
        });
    }

    for (let i in replacements) {
        const r = replacements[i];
        const label = document.getElementById(r.id).querySelector('label');

        if (checked) {
            label.innerText = r.other;
        } else {
            label.innerText = r.self;
        }
    }
}

refresh();
typeCheckbox.addEventListener('input', refresh);
