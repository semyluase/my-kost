const nameFoodSnack = document.querySelector("#name-food-snack");
const codeFoodSnack = document.querySelector("#code-food-snack");
const hargaFoodSnack = document.querySelector("#harga-food-snack");
const slugUploadFotoFoodSnack = document.querySelector(
    "#slug-upload-food-snack"
);

const fnFoodSnack = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-food-snack"),
            btnSave: document.querySelector("#btn-save-food-snack"),
        },
        dropdowns: {
            categoryDropdown: new Choices(
                document.querySelector("#category-food-snack")
            ),
        },
        dropzones: {
            uploadFoodSnackDropzone: new Dropzone(
                "#dropzone-food-snack-picture",
                {
                    url: `${baseUrl}/masters/food-snacks/upload-picture`,
                    method: "POST",
                    paramName: "files",
                    autoProcessQueue: true,
                    acceptedFiles: ".jpeg,.jpg,.png,.gif",
                    maxFiles: 1,
                    maxFilesize: 5, // MB
                    createImageThumbnails: true,
                    addRemoveLinks: true,
                }
            ),
        },
        modals: {
            modalFoodSnack: new bootstrap.Modal(
                document.querySelector("#modal-food-snack")
            ),
            modalUploadFotoFoodSnack: new bootstrap.Modal(
                document.querySelector("#modal-upload-picture-food-snack")
            ),
        },
        tables: {
            tbFoodSnack: $("#tb-food-snack").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/food-snacks/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    uploadPicture: async (codeItem) => {
        await fetch(`${baseUrl}/masters/food-snacks/upload-picture/${codeItem}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swalWithBootstrapButtons.fire(
                            "Terjadi kesalahan",
                            "Saat mengambil data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                slugUploadFotoFoodSnack.value = response.code_item;

                fnFoodSnack.init.dropzones.uploadFoodSnackDropzone.removeAllFiles(
                    true
                );
                files = Array();
                fnFoodSnack.init.modals.modalUploadFotoFoodSnack.show();
            });
    },

    onEdit: async (codeItem) => {
        await fetch(`${baseUrl}/masters/food-snacks/${codeItem}/edit`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swalWithBootstrapButtons.fire(
                            "Failed",
                            "Something error while get data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then(async (response) => {
                nameFoodSnack.value = response.name;
                hargaFoodSnack.value = response.price;
                codeFoodSnack.value = response.code_item;
                // fotoFoodSnack.value = "";
                // viewFotoFoodSnack.src = response.picture
                //     ? `${baseUrl}/assets/upload/foodSnack/${response.picture.file_name}`
                //     : `${baseUrl}/assets/image/nocontent.jpg`;

                await createDropdown(
                    [
                        {
                            label: "Makanan",
                            value: "F",
                        },
                        {
                            label: "Minuman",
                            value: "D",
                        },
                        {
                            label: "Makanan Ringan",
                            value: "S",
                        },
                    ],
                    fnFoodSnack.init.dropdowns.categoryDropdown,
                    "Pilih Kategori",
                    response.category
                );

                fnFoodSnack.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnFoodSnack.init.modals.modalFoodSnack.show();
            });
    },

    onDelete: async (codeItem, csrf) => {
        swalWithBootstrapButtons
            .fire({
                title: "Are you sure?",
                text: "You want to delete this data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    blockUI();

                    const results = await onSaveJson(
                        `${baseUrl}/masters/food-snacks/${codeItem}`,
                        JSON.stringify({
                            _token: csrf,
                        }),
                        "delete"
                    );

                    unBlockUI();

                    if (results.data.status) {
                        swalWithBootstrapButtons
                            .fire("success", results.data.message, "success")
                            .then(async (result) => {
                                if (result.isConfirmed) {
                                    fnFoodSnack.init.tables.tbFoodSnack.ajax
                                        .url(
                                            `${baseUrl}/masters/food-snacks/get-all-data`
                                        )
                                        .draw();
                                }
                            });
                    } else {
                        swalWithBootstrapButtons.fire(
                            "Failed",
                            results.data.message,
                            "error"
                        );
                    }
                }
            });
    },

    deletePicture: async (slug, csrf) => {
        swalWithBootstrapButtons
            .fire({
                title: "Apakah anda yakin?",
                text: "Anda akan menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "Tidak",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
            })
            .then(async (result) => {
                if (result.isConfirmed) {
                    blockUI();

                    const results = await onSaveJson(
                        `${baseUrl}/masters/food-snacks/delete-picture/${slug}`,
                        JSON.stringify({
                            _token: csrf,
                        }),
                        "delete"
                    );

                    unBlockUI();

                    if (results.data.status) {
                        swalWithBootstrapButtons
                            .fire("success", results.data.message, "success")
                            .then(async (result) => {
                                if (result.isConfirmed) {
                                    fnFoodSnack.init.tables.tbFoodSnack.ajax
                                        .url(
                                            `${baseUrl}/masters/food-snacks/get-all-data`
                                        )
                                        .draw();
                                }
                            });
                    } else {
                        swalWithBootstrapButtons.fire(
                            "Failed",
                            results.data.message,
                            "error"
                        );
                    }
                }
            });
    },
};

fnFoodSnack.init.buttons.btnAdd.addEventListener("click", async () => {
    nameFoodSnack.value = "";
    codeFoodSnack.value = "";
    hargaFoodSnack.value = "";

    // viewFotoFoodSnack.src = `${baseUrl}/assets/image/nocontent.jpg`;
    await createDropdown(
        [
            {
                label: "Makanan",
                value: "F",
            },
            {
                label: "Minuman",
                value: "D",
            },
            {
                label: "Makanan Ringan",
                value: "S",
            },
        ],
        fnFoodSnack.init.dropdowns.categoryDropdown,
        "Pilih Kategori",
        ""
    );

    fnFoodSnack.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnFoodSnack.init.modals.modalFoodSnack.show();
});

fnFoodSnack.init.buttons.btnSave.addEventListener("click", async () => {
    switch (fnFoodSnack.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/food-snacks`;

            data = JSON.stringify({
                name: nameFoodSnack.value,
                category:
                    fnFoodSnack.init.dropdowns.categoryDropdown.getValue(true),
                price: hargaFoodSnack.value,
                _token: fnFoodSnack.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/food-snacks/${codeFoodSnack.value}`;

            data = JSON.stringify({
                name: nameFoodSnack.value,
                category:
                    fnFoodSnack.init.dropdowns.categoryDropdown.getValue(true),
                price: hargaFoodSnack.value,
                _token: fnFoodSnack.init.buttons.btnSave.dataset.csrf,
            });

            method = "put";
            break;
    }

    blockUI();

    const results = await onSaveJson(url, data, method);

    unBlockUI();

    if (results.data.status) {
        swalWithBootstrapButtons
            .fire("Success", results.data.message, "success")
            .then((result) => {
                if (result.isConfirmed) {
                    fnFoodSnack.init.modals.modalFoodSnack.hide();

                    fnFoodSnack.init.tables.tbFoodSnack.ajax
                        .url(`${baseUrl}/masters/food-snacks/get-all-data`)
                        .draw();
                }
            });
    } else {
        if (results.data.message.name) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.name[0],
                "error"
            );

            return false;
        }

        if (results.data.message.price) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.price[0],
                "error"
            );

            return false;
        }

        if (results.data.message.category) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.category[0],
                "error"
            );

            return false;
        }

        if (typeof results.data.message == "string") {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message,
                "error"
            );

            return false;
        }
    }
});

fnFoodSnack.init.dropzones.uploadFoodSnackDropzone.on(
    "removedfile",
    function (file) {}
);

fnFoodSnack.init.dropzones.uploadFoodSnackDropzone.on(
    "success",
    (file, response) => {
        if (response.data.status) {
            swalWithBootstrapButtons
                .fire("Berhasil", response.data.message, "success")
                .then((result) => {
                    if (result.isConfirmed) {
                        fnFoodSnack.init.modals.modalUploadFotoFoodSnack.hide();
                        fnFoodSnack.init.tables.tbFoodSnack.ajax
                            .url(`${baseUrl}/masters/food-snacks/get-all-data`)
                            .draw();
                    }
                });
        } else {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                response.data.message,
                "error"
            );
        }
    }
);
