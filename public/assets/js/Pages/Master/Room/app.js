const nameRoom = document.querySelector("#name-room");
const slugRoom = document.querySelector("#slug-room");
const dailyPriceRoom = document.querySelector("#room-price-daily");
const weeklyPriceRoom = document.querySelector("#room-price-weekly");
const monthlyPriceRoom = document.querySelector("#room-price-monthly");
const yearlyPriceRoom = document.querySelector("#room-price-yearly");
const slugUploadRoom = document.querySelector("#slug-upload-room");
const viewGambarRoom = document.querySelector("#view-gambar-kamar");

const fnRoom = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-room"),
            btnSave: document.querySelector("#btn-save-room"),
        },
        dropdowns: {
            homeDropdown: new Choices(document.querySelector("#home-room")),
            categoryDropdown: new Choices(
                document.querySelector("#category-room")
            ),
            searchRoomCategory: new Choices(
                document.querySelector("#search-room-category")
            ),
        },
        modals: {
            modalRoom: new bootstrap.Modal(
                document.querySelector("#modal-room")
            ),
            modalViewRoomPicture: new bootstrap.Modal(
                document.querySelector("#modal-view-picture-room")
            ),
        },
        tables: {
            tbRoom: $("#tb-room").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/rooms/get-all-data?category=`,
                },
                processing: true,
                ordering: false,
                paging: false,
                scrollX: true,
            }),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-categories`,
            fnRoom.init.dropdowns.searchRoomCategory,
            "Search Category",
            ""
        );
    },

    viewPicture: async (slug) => {
        blockUI();
        await fetch(`${baseUrl}/masters/rooms/view-picture/${slug}`)
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
                viewGambarRoom.innerHTML = "";
                viewGambarRoom.innerHTML = response;

                fnRoom.init.modals.modalViewRoomPicture.show();
            });
    },

    onEdit: async (slug) => {
        await fetch(`${baseUrl}/masters/rooms/${slug}/edit`)
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
                blockUI();
                nameRoom.value = response.number_room;
                slugRoom.value = response.slug;

                await createDropdown(
                    `${baseUrl}/utils/dropdowns/get-homes`,
                    fnRoom.init.dropdowns.homeDropdown,
                    "Rumah Kos",
                    response.home_id
                );

                await createDropdown(
                    `${baseUrl}/utils/dropdowns/get-categories`,
                    fnRoom.init.dropdowns.categoryDropdown,
                    "Category",
                    response.category_id
                );

                fnRoom.init.buttons.btnSave.setAttribute(
                    "data-type",
                    "edit-data"
                );
                fnRoom.init.modals.modalRoom.show();

                unBlockUI();
            });
    },

    onDelete: async (slug, csrf) => {
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
                        `${baseUrl}/masters/rooms/${slug}`,
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

fnRoom.onLoad();

fnRoom.init.dropdowns.searchRoomCategory.passedElement.element.addEventListener(
    "change",
    () => {
        fnRoom.init.tables.tbRoom.ajax
            .url(
                `${baseUrl}/masters/rooms/get-all-data?category=${fnRoom.init.dropdowns.searchRoomCategory.getValue(
                    true
                )}`
            )
            .load();
    }
);

fnRoom.init.buttons.btnAdd.addEventListener("click", async () => {
    blockUI();
    nameRoom.value = "";
    slugRoom.value = "";

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-homes`,
        fnRoom.init.dropdowns.homeDropdown,
        "Rumah Kos",
        ""
    );

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-categories`,
        fnRoom.init.dropdowns.categoryDropdown,
        "Category",
        ""
    );

    fnRoom.init.buttons.btnSave.setAttribute("data-type", "add-data");
    fnRoom.init.modals.modalRoom.show();

    unBlockUI();
});

fnRoom.init.buttons.btnSave.addEventListener("click", async () => {
    const roomFacility = document.querySelectorAll(
        "input[name='room-facility']"
    );

    let roomFacilities = Array();
    roomFacility.forEach((rf) => {
        if (rf.checked) {
            roomFacilities.push(rf.value);
        }
    });

    switch (fnRoom.init.buttons.btnSave.dataset.type) {
        case "add-data":
            url = `${baseUrl}/masters/rooms`;

            data = JSON.stringify({
                name: nameRoom.value,
                category: fnRoom.init.dropdowns.categoryDropdown.getValue(true),
                home: fnRoom.init.dropdowns.homeDropdown.getValue(true),
                _token: fnRoom.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        case "edit-data":
            url = `${baseUrl}/masters/rooms/${slugRoom.value}`;

            data = JSON.stringify({
                name: nameRoom.value,
                category: fnRoom.init.dropdowns.categoryDropdown.getValue(true),
                home: fnRoom.init.dropdowns.homeDropdown.getValue(true),
                _token: fnRoom.init.buttons.btnSave.dataset.csrf,
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
                    fnRoom.init.modals.modalRoom.hide();

                    fnRoom.init.tables.tbRoom.ajax
                        .url(`${baseUrl}/masters/rooms/get-all-data`)
                        .load();
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

        if (results.data.message.slug) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.slug[0],
                "error"
            );

            return false;
        }

        if (results.data.message.location) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.location[0],
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

        if (results.data.message.facilities) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.facilities[0],
                "error"
            );

            return false;
        }

        if (results.data.message.rules) {
            swalWithBootstrapButtons.fire(
                "Something Wrong",
                results.data.message.rules[0],
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
