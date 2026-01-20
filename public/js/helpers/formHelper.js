const formHelper = {
    initialState: {},

    init(form) {
        this.initialState = this.getFormData(form);
    },

    getFormData(form) {
        const data = {};
        new FormData(form).forEach((value, key) => {
            if (form.elements[key].type === "hidden") return;
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
                form.elements[key].value = "";
            }
        });
    },

    async fetchAndFillForm(url, form) {
        return fetch(url, {
            headers: { Accept: "application/json" },
        })
            .then(async (res) => {
                const body = await res.json();
                if (!res.ok) throw body.message || "Terjadi kesalahan.";
                return body;
            })
            .then(({ data }) => {
                if (!data || !form) return null;

                Object.entries(data).forEach(([key, value]) => {
                    if (form.elements[key]) {
                        form.elements[key].value = value ?? "";
                    }
                });

                return data;
            }).catch((error) => {
                toastHelper.showToast(false, error);
                throw error;
            });
    },

    beforeSubmit(form) {
        if(!this.isFormChanged(form)){
            throw "Tidak ada perubahan.";
        }
    },

    afterSubmit(modal) {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
    },

    async submitForm(url, form, options = {}) {
        try {
            const {modal, beforeSubmit, redirectUrl} = options;
            
            if (beforeSubmit) {
                this.beforeSubmit(form);
            }

            const payload = this.getFormData(form);

            const res = await fetch(url, {
                method: form.querySelector('input[name="_method"]')?.value,
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": form.querySelector(
                        'input[name="_token"]'
                    )?.value,
                },
                body: JSON.stringify(payload),
            });

            const body = await res.json();

            if (!res.ok) {
                throw body.message || "Terjadi kesalahan.";
            } 

            if (redirectUrl) {
                toastHelper.persist({
                    success: body.success,
                    message: body.message,
                });
                window.location.href = redirectUrl;
                return;
            }

            toastHelper.showToast(
                body.success, 
                body.message
            );

            this.afterSubmit(modal);

        } catch (error) {
            toastHelper.showToast(false, error);
            throw error;
        }
    },
};
