const toastHelper = {
    persist(data) {
        sessionStorage.setItem("toast", JSON.stringify(data));
    },

    restore() {
        const data = sessionStorage.getItem("toast");
        if (!data) return null;

        sessionStorage.removeItem("toast");
        return JSON.parse(data);
    },

    showToast: (success = true, message = "Toast") => {
        const toastEl = document.getElementById("liveToast");
        if (!toastEl) return;

        const toastBody = toastEl.querySelector(".toast-body");

        toastBody.textContent = message;

        toastBody.classList.add(success ? "text-bg-success" : "text-bg-danger");

        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        toast.show();
    },
};
