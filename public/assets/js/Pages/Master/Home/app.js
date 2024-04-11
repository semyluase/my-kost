const nameHome = document.querySelector("#name-home");
const slugHome = document.querySelector("#slug-home");
const langLongHome = document.querySelector("#lang-long-home");
const cityHome = document.querySelector("#city-home");
const addressHome = document.querySelector("#address-home");
const slugUploadHome = document.querySelector("#slug-upload-home");
const viewGambarHome = document.querySelector("#view-gambar-home");

let url, data, method;

const fnHome = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-home"),
            btnSave: document.querySelector("#btn-save-home"),
            btnSaveUploadPictureHome: document.querySelector(
                "#btn-save-upload-home"
            ),
        },
        modals: {
            modalHome: new bootstrap.Modal(
                document.querySelector("#modal-home")
            ),
            modalUploadHomePicture: new bootstrap.Modal(
                document.querySelector("#modal-upload-picture-home")
            ),
            modalViewHomePicture: new bootstrap.Modal(
                document.querySelector("#modal-view-picture-home")
            ),
        },
        dropzones: {
            uploadHomeDropzone: new Dropzone("#dropzone-home-picture", {
                url: `${baseUrl}/masters/homes/upload-picture`,
                method: "POST",
                paramName: "files",
                autoProcessQueue: true,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                maxFiles: 100,
                maxFilesize: 5, // MB
                uploadMultiple: true,
                parallelUploads: 100, // use it with uploadMultiple
                createImageThumbnails: true,
                addRemoveLinks: true,
            }),
        },
        tables: {
            tbHome: $("#tb-home").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/homes/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },

    onActivated: async (slug, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/masters/homes/${slug}`,
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
                        fnHome.init.tables.tbHome.ajax
                            .url(`${baseUrl}/masters/homes/get-all-data`)
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

    uploadPicture: async (slug) => {
        await fetch(`${baseUrl}/masters/homes/upload-picture/${slug}`)
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
                slugUploadHome.value = response.slug;

                fnHome.init.dropzones.uploadHomeDropzone.removeAllFiles(true);
                files = Array();
                fnHome.init.modals.modalUploadHomePicture.show();
            });
    },

    viewPicture: async (slug) => {
        blockUI();
        await fetch(`${baseUrl}/masters/homes/view-picture/${slug}`)
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
                viewGambarHome.innerHTML = "";
                viewGambarHome.innerHTML = response;

                fnHome.init.modals.modalViewHomePicture.show();
            });
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/masters/homes/${slug}/edit`)
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
                nameHome.value = response.name;
                slugHome.value = response.slug;
                langLongHome.value =
                    response.langitute + "," + response.longitute;
                cityHome.value = response.city;
                addressHome.value = response.address;

                const sharedFacility = document.querySelectorAll(
                    "input[name='shared-facility']"
                );

                const rulesList =
                    document.querySelectorAll("input[name='rule']");

                sharedFacility.forEach((sf) => {
                    if (response.shared_facility.length > 0) {
                        response.shared_facility.forEach((share) => {
                            if (sf.value == share.facility.id) {
                                sf.checked = true;
                            } else {
                                sf.checked = false;
                            }
                        });
                    } else {
                        sf.checked = false;
                    }
                });

                rulesList.forEach((r) => {
                    if (response.rule.length > 0) {
                        response.rule.forEach((rl) => {
                            if (r.value == rl.rule.id) {
                                r.checked = true;
                            } else {
                                r.checked = false;
                            }
                        });
                    } else {
                        r.checked = false;
                    }
                });

                fnHome.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnHome.init.modals.modalHome.show();
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
                        `${baseUrl}/masters/homes/${slug}`,
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
                                    fnHome.init.tables.tbHome.ajax
                                        .url(
                                            `${baseUrl}/masters/homes/get-all-data`
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
                        `${baseUrl}/masters/homes/delete-picture/${slug}`,
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
                                    fnHome.init.tables.tbHome.ajax
                                        .url(
                                            `${baseUrl}/masters/homes/get-all-data`
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

fnHome.init.buttons.btnAdd.addEventListener("click", () => {
    nameHome.value = "";
    slugHome.value = "";
    langLongHome.value = "";
    cityHome.value = "";
    addressHome.value = "";

    const sharedFacility = document.querySelectorAll(
        "input[name='shared-facility']"
    );

    const rulesList = document.querySelectorAll("input[name='rule']");

    sharedFacility.forEach((sf) => {
        if (sf.checked) {
            sf.checked = false;
        }
    });

    rulesList.forEach((r) => {
        if (r.checked) {
            r.checked = false;
        }
    });

    fnHome.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnHome.init.modals.modalHome.show();
});

fnHome.init.buttons.btnSave.addEventListener("click", async () => {
    const sharedFacility = document.querySelectorAll(
        "input[name='shared-facility']"
    );

    const rulesList = document.querySelectorAll("input[name='rule']");

    let sharedFacilities = Array(),
        rules = Array();

    sharedFacility.forEach((sf) => {
        if (sf.checked) {
            sharedFacilities.push(sf.value);
        }
    });

    rulesList.forEach((r) => {
        if (r.checked) {
            rules.push(r.value);
        }
    });
    switch (fnHome.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/homes`;

            data = JSON.stringify({
                name: nameHome.value,
                langLong: langLongHome.value,
                city: cityHome.value,
                address: addressHome.value,
                sharedFacilities: sharedFacilities,
                rules: rules,
                _token: fnHome.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/homes/${slugHome.value}`;

            data = JSON.stringify({
                name: nameHome.value,
                langLong: langLongHome.value,
                city: cityHome.value,
                address: addressHome.value,
                sharedFacilities: sharedFacilities,
                rules: rules,
                _token: fnHome.init.buttons.btnSave.dataset.csrf,
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
                    fnHome.init.modals.modalHome.hide();

                    fnHome.init.tables.tbHome.ajax
                        .url(`${baseUrl}/masters/homes/get-all-data`)
                        .draw();
                }
            });
    } else {
        if (results.data.message.name[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.name[0],
                "error"
            );

            return false;
        }

        if (results.data.message.slug[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.slug[0],
                "error"
            );

            return false;
        }

        if (results.data.message.city[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.city[0],
                "error"
            );

            return false;
        }

        if (results.data.message.address[0]) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.address[0],
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

fnHome.init.dropzones.uploadHomeDropzone.on("removedfile", function (file) {});

fnHome.init.dropzones.uploadHomeDropzone.on(
    "successmultiple",
    (file, response) => {
        if (response.data.status) {
            swalWithBootstrapButtons
                .fire("Berhasil", response.data.message, "success")
                .then((result) => {
                    if (result.isConfirmed) {
                        fnHome.init.modals.modalUploadHomePicture.hide();
                        fnHome.init.tables.tbHome.ajax
                            .url(`${baseUrl}/masters/homes/get-all-data`)
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
