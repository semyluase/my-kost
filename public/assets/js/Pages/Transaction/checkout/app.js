const noRekInput = document.querySelector("#no-rek");
const pengembalianInput = document.querySelector("#pengembalian");
const jenisPengembalianRadio = document.querySelectorAll("#jenis-pengembalian");

let url, data, method, nomorKamar;

const fnCheckout = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
        dropdowns: {
            bankDropdown: new Choices(document.querySelector("#bank"), {
                shouldSort: false,
            }),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-bank`,
            fnCheckout.init.dropdowns.bankDropdown,
            "",
            ""
        );
    },
};

fnCheckout.onLoad();

fnCheckout.init.buttons.btnSave.addEventListener("click", async () => {
    let jenisPengembalian = "cash";

    jenisPengembalianRadio.forEach((item) => {
        if (item.checked) {
            jenisPengembalian = item.value;
        }
    });

    url = `${baseUrl}/transactions/rent-rooms/checkout`;

    data = JSON.stringify({
        noKamar: nomorKamar,
        jenisPengembalian: jenisPengembalian,
        bank:
            fnCheckout.init.dropdowns.bankDropdown.getValue(true) == undefined
                ? ""
                : fnCheckout.init.dropdowns.bankDropdown.getValue(true),
        noRek: noRekInput.value,
        pengembalian: pengembalianInput.value,
        _token: fnCheckout.init.buttons.btnSave.dataset.csrf,
    });

    method = "post";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swal.fire("Berhasil", results.data.message, "success").then(
            (result) => {
                if (result.isConfirmed) {
                    window.location.href = `${baseUrl}/transactions/rent-rooms`;
                }
            }
        );
    } else {
        if (jenisPengembalian == "transfer") {
            if (results.data.message.bank[0]) {
                swal.fire(
                    "Terjadi kesalahan",
                    results.data.message.bank[0],
                    "error"
                );
                return false;
            }

            if (results.data.message.noRek[0]) {
                swal.fire(
                    "Terjadi kesalahan",
                    results.data.message.noRek[0],
                    "error"
                );
                return false;
            }
        }

        if (results.data.message.pengembalian[0]) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.pengembalian[0],
                "error"
            );
            return false;
        }

        if (typeof results.data.message == "string") {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
            return false;
        }
    }
});
