const formHelper = {
    initialState: {},

    init(form) {
        this.initialState = this.getFormData(form);
    },

    getFormData(form) {
        const data = {};
        new FormData(form).forEach((value, key) => {
            if (form.elements[key].type === 'hidden') return;
            data[key] = value.trim();
        });
        return data;
    },

    isFormChanged(form) {
        const initialState = this.initialState;
        const currentState = this.getFormData(form);
        const keys = Object.keys(initialState);
        return keys.some((key) => initialState[key] !== currentState[key]);
    },

    resetForm(form) {
        Object.entries(this.initialState).forEach(([key]) => {
            if (form.elements[key]) {
                form.elements[key].value = '';
            }
        });
    }
};
