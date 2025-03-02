let url, data, method, noKamarLama;

const fnChangeRoom = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
        dropdowns: {
            categoryKamarDropdown: new Choices(
                document.querySelector("#category-kamar")
            ),
            nomorKamarDropdown: new Choices(
                document.querySelector("#nomor-kamar")
            ),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-categories-transaction`,
            fnChangeRoom.init.dropdowns.categoryKamarDropdown,
            "",
            ""
        );

        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room-by-category?category=`,
            fnChangeRoom.init.dropdowns.nomorKamarDropdown,
            "",
            ""
        );
    },
};

fnChangeRoom.onLoad();

fnChangeRoom.init.dropdowns.categoryKamarDropdown.passedElement.element.addEventListener(
    "change",
    async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-room-by-category?category=${fnChangeRoom.init.dropdowns.categoryKamarDropdown.getValue(
                true
            )}`,
            fnChangeRoom.init.dropdowns.nomorKamarDropdown,
            "",
            ""
        );
    }
);

fnChangeRoom.init.buttons.btnSave.addEventListener("click", async () => {
    url = `${baseUrl}/transactions/rent-rooms/change-room`;

    data = JSON.stringify({
        noKamarLama: noKamarLama,
        noKamarBaru:
            fnChangeRoom.init.dropdowns.nomorKamarDropdown.getValue(true),
        _token: fnChangeRoom.init.buttons.btnSave.dataset.csrf,
    });

    method = "post";

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        if (results.data.url != null) {
            swal.fire("Berhasil", results.data.message, "success").then(
                (result) => {
                    if (result.isConfirmed) {
                        window.location.href = `${baseUrl}/transactions/rent-rooms`;
                    }
                }
            );
        } else {
            window.location.href = `${baseUrl}${results.data.url}`;
        }
    } else {
        if (results.data.message.noKamarBaru[0]) {
            swal.fire(
                "Terjadi kesalahan",
                results.data.message.noKamarBaru[0],
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
