import {trans} from './translator';

const replacements = [
    {
        id: 'field-firstname',
        self: trans('request.vulnerable.firstname.self'),
        other: trans('request.vulnerable.firstname.other'),
    },
    {
        id: 'field-lastname',
        self: trans('request.vulnerable.lastname.self'),
        other: trans('request.vulnerable.lastname.other'),
    },
    {
        id: 'field-zipcode',
        self: trans('request.vulnerable.zipcode.self'),
        other: trans('request.vulnerable.zipcode.other'),
    },
    {
        id: 'field-email',
        self: trans('request.vulnerable.email.self'),
        other: trans('request.vulnerable.email.other'),
    },
];

const typeCheckbox = document.getElementById('vulnerable_help_request_isCloseOne');
const fieldCc = document.getElementById('field-other');

function refresh() {
    const checked = typeCheckbox.checked;

    if (checked) {
        fieldCc.style.display = 'block';

        const inputs = fieldCc.querySelectorAll('input');
        for (let i in inputs) {
            inputs[i].required = true;
        }
    } else {
        fieldCc.style.display = 'none';

        const inputs = fieldCc.querySelectorAll('input');
        for (let i in inputs) {
            inputs[i].required = false;
        }
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
typeCheckbox.addEventListener('change', refresh);
