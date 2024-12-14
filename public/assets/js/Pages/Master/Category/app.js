const nameCategory = document.querySelector("#name-category");
const slugCategory = document.querySelector("#slug-category");
const priceDailyCategoryInput = document.querySelector("#category-price-daily");
const priceWeeklyCategoryInput = document.querySelector(
    "#category-price-weekly"
);
const priceMonthlyCategoryInput = document.querySelector(
    "#category-price-monthly"
);
const priceYearlyCategoryInput = document.querySelector(
    "#category-price-yearly"
);
const slugUploadCategory = document.querySelector("#slug-upload-category");
const viewGambarCategory = document.querySelector("#view-gambar-kategori");

let files = Array();

const fnCategory = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-category"),
            btnSave: document.querySelector("#btn-save-category"),
            btnSaveUploadPictureCategory: document.querySelector(
                "#btn-save-upload-category"
            ),
        },
        modals: {
            modalCategory: new bootstrap.Modal(
                document.querySelector("#modal-category")
            ),
            modalUploadCategoryPicture: new bootstrap.Modal(
                document.querySelector("#modal-upload-picture-category")
            ),
            modalViewCategoryPicture: new bootstrap.Modal(
                document.querySelector("#modal-view-picture-category")
            ),
        },
        dropzones: {
            uploadCategoryDropzone: new Dropzone("#dropzone-category-picture", {
                url: `${baseUrl}/masters/categories/upload-picture`,
                method: "POST",
                paramName: "files",
                autoProcessQueue: true,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                maxFiles: 100,
                maxFilesize: 10, // MB
                uploadMultiple: true,
                parallelUploads: 10, // use it with uploadMultiple
                createImageThumbnails: true,
                addRemoveLinks: true,
            }),
        },
        tables: {
            tbCategory: $("#tb-category").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/categories/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    uploadPicture: async (slug) => {
        await fetch(`${baseUrl}/masters/categories/upload-picture/${slug}`)
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
                slugUploadCategory.value = response.slug;

                fnCategory.init.dropzones.uploadCategoryDropzone.removeAllFiles(
                    true
                );
                files = Array();
                fnCategory.init.modals.modalUploadCategoryPicture.show();
            });
    },

    viewPicture: async (slug) => {
        blockUI();
        await fetch(`${baseUrl}/masters/categories/view-picture/${slug}`)
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();
                    throw new Error(
                        swalWithBootstrapButtons.fire(
                            "Terjadi kesalahan",
                            "Saat pengambilan data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                unBlockUI();
                viewGambarCategory.innerHTML = "";
                viewGambarCategory.innerHTML = response;

                fnCategory.init.modals.modalViewCategoryPicture.show();
            });
    },

    onActivated: async (slug, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/masters/categories/${slug}`,
            JSON.stringify({
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swalWithBootstrapButtons
                .fire("Success", results.data.message, "success")
                .then((result) => {
                    if (result.isConfirmed) {
                        fnCategory.init.tables.tbCategory.ajax
                            .url(`${baseUrl}/masters/categories/get-all-data`)
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
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/masters/categories/${slug}/edit`)
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
            .then((response) => {
                nameCategory.value = response.name;
                slugCategory.value = response.slug;

                const categoryFacilities = document.querySelectorAll(
                    "input[name='category-facility']"
                );

                categoryFacilities.forEach((rf) => {
                    response.facilities.forEach((rr) => {
                        if (rr.facility_id == rf.value) {
                            rf.checked = true;
                        }
                    });
                });

                response.prices.forEach((rp) => {
                    switch (rp.type) {
                        case "daily":
                            priceDailyCategoryInput.value = rp.price;
                            break;

                        case "weekly":
                            priceWeeklyCategoryInput.value = rp.price;
                            break;

                        case "monthly":
                            priceMonthlyCategoryInput.value = rp.price;
                            break;

                        case "yearly":
                            priceYearlyCategoryInput.value = rp.price;
                            break;

                        default:
                            break;
                    }
                });

                fnCategory.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnCategory.init.modals.modalCategory.show();
            });
    },

    onDelete: async (slug, csrf) => {
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
                        `${baseUrl}/masters/categories/${slug}`,
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
                                    fnCategory.init.tables.tbCategory.ajax
                                        .url(
                                            `${baseUrl}/masters/categories/get-all-data`
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
                        `${baseUrl}/masters/categories/delete-picture/${slug}`,
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
                                    fnRoom.init.tables.tbRoom.ajax
                                        .url(
                                            `${baseUrl}/masters/rooms/get-all-data`
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

fnCategory.init.buttons.btnAdd.addEventListener("click", () => {
    nameCategory.value = "";
    slugCategory.value = "";
    priceDailyCategoryInput.value = "";
    priceWeeklyCategoryInput.value = "";
    priceMonthlyCategoryInput.value = "";
    priceYearlyCategoryInput.value = "";

    const categoryFacility = document.querySelectorAll(
        "input[name='category-facility']"
    );

    categoryFacility.forEach((cf) => {
        if (cf.checked) {
            cf.checked = false;
        }
    });

    fnCategory.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnCategory.init.modals.modalCategory.show();
});

fnCategory.init.buttons.btnSave.addEventListener("click", async () => {
    const categoryFacility = document.querySelectorAll(
        "input[name='category-facility']"
    );

    let categoryFacilities = Array();
    categoryFacility.forEach((rf) => {
        if (rf.checked) {
            categoryFacilities.push(rf.value);
        }
    });
    switch (fnCategory.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/categories`;

            data = JSON.stringify({
                name: nameCategory.value,
                categoryFacilities: categoryFacilities,
                dailyPrice: priceDailyCategoryInput.value,
                weeklyPrice: priceWeeklyCategoryInput.value,
                monthlyPrice: priceMonthlyCategoryInput.value,
                yearlyPrice: priceYearlyCategoryInput.value,
                _token: fnCategory.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/categories/${slugCategory.value}`;

            data = JSON.stringify({
                name: nameCategory.value,
                categoryFacilities: categoryFacilities.value,
                dailyPrice: priceDailyCategoryInput.value,
                weeklyPrice: priceWeeklyCategoryInput.value,
                monthlyPrice: priceMonthlyCategoryInput.value,
                yearlyPrice: priceYearlyCategoryInput.value,
                _token: fnCategory.init.buttons.btnSave.dataset.csrf,
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
                    fnCategory.init.modals.modalCategory.hide();

                    fnCategory.init.tables.tbCategory.ajax
                        .url(`${baseUrl}/masters/categories/get-all-data`)
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

        if (results.data.message.categoryFacilities) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.categoryFacilities[0],
                "error"
            );

            return false;
        }

        if (results.data.message.slug) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.slug[0],
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

fnCategory.init.dropzones.uploadCategoryDropzone.on(
    "removedfile",
    function (file) {}
);

fnCategory.init.dropzones.uploadCategoryDropzone.on(
    "successmultiple",
    (file, response) => {
        if (response.data.status) {
            swalWithBootstrapButtons
                .fire("Berhasil", response.data.message, "success")
                .then((result) => {
                    if (result.isConfirmed) {
                        fnCategory.init.modals.modalUploadCategoryPicture.hide();
                        fnCategory.init.tables.tbCategory.ajax
                            .url(`${baseUrl}/masters/categories/get-all-data`)
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

fnCategory.init.buttons.btnSaveUploadPictureCategory.addEventListener(
    "click",
    {}
);
