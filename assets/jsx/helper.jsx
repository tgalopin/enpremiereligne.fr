const babysitCheckbox = document.getElementById('helper_canBabysit');
const babysitCriteria = document.getElementById('babysit-fields');

function refresh() {
    if (babysitCheckbox.checked) {
        babysitCriteria.style.opacity = '1.0';
    } else {
        babysitCriteria.style.opacity = '0.5';
    }
}

refresh();
babysitCheckbox.addEventListener('input', refresh);
