import { Controller } from '@hotwired/stimulus';
import { Autocomplete } from '@materializecss/materialize';

export default class extends Controller {
    static targets = ['input', 'formInput', 'result']

    timeout = null;
    autocompleteElement = null;
    items = [];

    connect() {
        let self = this;

        this.autocompleteElement = M.Autocomplete.init(this.inputTarget, {
            onAutocomplete: function (item) {
                self.onAutocomplete(item);
            }
        });

        if (this.formInputTarget.value.length > 0) {
            let values = JSON.parse(this.formInputTarget.value);
            let currentItems = [];

            for (const item of values) {
                this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item));
                currentItems.push(item.id);
            }

            this.formInputTarget.value = JSON.stringify(currentItems);
        }
    }

    remove(event) {
        let existingItems = JSON.parse(this.formInputTarget.value);
        let relatedItem = event.target.closest('.related-item');
        let index = existingItems.indexOf(relatedItem.dataset.id);

        if (index > -1) {
            existingItems.splice(index, 1);
            relatedItem.remove();
        }

        this.formInputTarget.value = JSON.stringify(existingItems);
    }

    preventDefault(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    }

    autocomplete(event) {
        this.autocompleteElement.updateData({});
        clearTimeout(this.timeout);
        let value = this.inputTarget.value;
        let self = this;

        if (value) {
            this.timeout = setTimeout(function () {
                fetch('/items/autocomplete/' + value, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(function(results) {
                    self.items = results;
                    let data = {};
                    for (const result of results) {
                        data[result.name] = result.thumbnail;
                    }
                    self.autocompleteElement.updateData(data);
                    self.autocompleteElement.open();
                })
            }, 500);
        }
    }

    onAutocomplete(item) {
        let existingItems = JSON.parse(this.formInputTarget.value);
        for (const object of this.items) {
            if (object.name === item) {
                item = object;
            }
        }

        let index = existingItems.indexOf(item.id);
        if (index === -1) {
            existingItems.push(item.id);
            this.resultTarget.insertAdjacentHTML('beforeend', this.getChip(item));
        }

        this.formInputTarget.value = JSON.stringify(existingItems);
        this.inputTarget.value = '';
    }

    getChip(item) {
        const thumbnail = item.thumbnail ? item.thumbnail : '/build/images/default.png';
        return '<tr class="related-item" data-id="' + item.id + '" data-text="' + item.name + '">' +
            '<td><img src="' + thumbnail + '"></td>' +
            '<td>' + item.name + '</td>' +
            '<td><i data-action="click->autocomplete--item#remove" class="fa fa-times close"></i></td>' +
        '</tr>';
    }
}
