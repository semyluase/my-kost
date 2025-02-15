const fnDetailSewa = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
    },
};

fnDetailSewa.init.buttons.btnSave.addEventListener("click", async () => {
    swalWithBootstrapButtons
        .fire({
            title: "Apakah penyewa sudah membayar?",
            text: "Harap pastikan penyewa sudah membayar sesuai total harga",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya",
        })
        .then(async (result) => {
            if (result.isConfirmed) {
                blockUI();
                const results = await onSaveJson(
                    `${baseUrl}/transactions/rent-rooms/detail-rents/${fnDetailSewa.init.buttons.btnSave.dataset.room}`,
                    JSON.stringify({
                        _token: fnDetailSewa.init.buttons.btnSave.dataset.csrf,
                    }),
                    "post"
                );
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
                    swal.fire(
                        "Terjadi kesalahan",
                        results.data.message,
                        "error"
                    );
                }
            }
        });
});
