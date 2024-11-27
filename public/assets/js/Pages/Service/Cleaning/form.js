const nobuktiInput = document.querySelector("#nobukti");
const jamRequestInput = document.querySelector("#waktu-cleaning");
const jamMulaiInput = document.querySelector("#mulai-cleaning");
const jamSelesaiInput = document.querySelector("#selesai-cleaning");

let url, data, method;

const fnCleaning = {
    init: {
        buttons: {
            btnSimpan: document.querySelector("#btn-save"),
        },
        dropdowns: {
            kamarDropdown: new Choices(document.querySelector("#no-kamar")),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room`,
            fnCleaning.init.dropdowns.kamarDropdown,
            "Pilih Kamar",
            noKamar
        );

        if (jamSelesaiInput.value != "") {
            fnCleaning.init.buttons.btnSimpan.setAttribute("disabled", true);
        }
    },
};

fnCleaning.onLoad();

fnCleaning.init.buttons.btnSimpan.addEventListener("click", async () => {
    url = `${baseUrl}/transactions/orders/cleaning`;

    method = "post";

    data = JSON.stringify({
        nobukti: nobuktiInput.value,
        noKamar: fnCleaning.init.dropdowns.kamarDropdown.getValue(true),
        jamRequest: jamRequestInput.value,
        jamMulai: jamMulaiInput.value,
        jamSelesai: jamSelesaiInput.value,
        _token: fnCleaning.init.buttons.btnSimpan.dataset.csrf,
    });

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        Toastify({
            text: results.data.message,
            className: "success",
            style: {
                background: "rgb(47, 179, 68)",
            },
        }).showToast();

        if (nobuktiInput.value == "") {
            window.history.pushState(
                null,
                null,
                `${baseUrl}/transactions/orders/cleaning/create?nobukti=${results.data.nobukti}`
            );

            nobuktiInput.value = results.data.nobukti;
        }

        fnCleaning.onLoad();
    } else {
        swal.fire("Terjadi kesalahan", results.data.message, "error");
    }
});
