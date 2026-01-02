const requestHelper = {
    async fetchAndFillForm(url, form) {
        return fetch(url, {
            headers: { Accept: 'application/json' }
        })
        .then(async res => {
            const body = await res.json();
            if (!res.ok) throw body;
            return body;
        })
        .then(({ data }) => {
            if (!data || !form) return null;

            Object.entries(data).forEach(([key, value]) => {
                if (form.elements[key]) {
                form.elements[key].value = value ?? '';
                }
            });

            return data;
        });
    },

    submitForm(url, form) {

    },

};