import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['sorter']

    sort(event) {
        const column = event.target.dataset.column;
        const direction = event.target.dataset.direction;
        const values = this.element.querySelectorAll(`tbody td[data-column="${column}"] span[data-value]`);

        let collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
        let orderedValues;

        if (direction === 'asc') {
            orderedValues = Array.from(values).sort((a, b) => collator.compare(a.dataset.value, b.dataset.value))
        } else {
            orderedValues = Array.from(values).sort((a, b) => collator.compare(b.dataset.value, a.dataset.value))
        }

        orderedValues.forEach((value, index) => {
            this.element.querySelector('tbody').appendChild(value.closest('tr'));
        });

        this.sorterTargets.forEach(sorter => {
           sorter.classList.remove('active');
        });
        event.target.classList.add('active')
    }
}
