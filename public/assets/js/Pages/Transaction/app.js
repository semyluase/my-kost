const viewRadio = document.querySelectorAll("input[type=radio][name=btn-view]");
const tableView = document.querySelector("#table-view");
const calendarView = document.querySelector("#calendar-view");

let room = null;

const fnTransactionRoom = {
    init: {
        buttons: {
            btnListMember: document.querySelector("#btn-list-member"),
        },
        dropdowns: {
            categoryRoomDropdown: new Choices(
                document.querySelector("#category-room")
            ),
            categoryCalendarDropdown: new Choices(
                document.querySelector("#category-calendar")
            ),
            roomCalendarDropdown: new Choices(
                document.querySelector("#room-calendar")
            ),
        },
        calendars: {
            // calendarFull: new FullCalendar.Calendar(
            //     document.querySelector("#calendar-section"),
            //     {
            //         initialView: "dayGridMonth",
            //         locale: "id",
            //         selectable: true,
            //         eventSources: [
            //             {
            //                 url: `${baseUrl}/transactions/rent-rooms/calendar-views?room=${room}`,
            //             },
            //         ],
            //         datesSet: (info) => {
            //             startDate = moment(info.start).format("YYYY-MM-DD");
            //             endDate = moment(info.end).format("YYYY-MM-DD");
            //         },
            //         select: (info) => {
            //             // modalTitle.innerHTML =
            //             //     "Plan Export " +
            //             //     moment(info.startStr).format("DD MMMM YYYY");
            //             // tanggalStartModal.value = moment(info.startStr).format(
            //             //     "YYYY-MM-DD"
            //             // );
            //             // tanggalEndModal.value = moment(info.startStr).format(
            //             //     "YYYY-MM-DD"
            //             // );
            //             // fnPlanExport.init.modals.modalUploadPlan.show();
            //         },
            //     }
            // ).render(),
        },
        modals: {
            modalListMember: new bootstrap.Modal(
                document.querySelector("#modal-list-member")
            ),
            modalDetailTransactionRoom: new bootstrap.Modal(
                document.querySelector("#modal-detail-room-transaction")
            ),
        },
        tables: {
            tbRoom: new $("#tb-room").DataTable({
                ajax: {
                    url: `${baseUrl}/transactions/rent-rooms/get-all-data?category=`,
                },
                processing: true,
                paging: false,
            }),
            tbListMember: new $("#tb-list-member").DataTable({
                ajax: {
                    url: `${baseUrl}/members/get-all-data`,
                },
                processing: true,
                paging: false,
            }),
        },
    },
    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-categories-transaction`,
            fnTransactionRoom.init.dropdowns.categoryRoomDropdown,
            "Pilih Kategori",
            ""
        );

        // await createDropdown(
        //     `${baseUrl}/utils/dropdowns/get-categories-transaction`,
        //     fnTransactionRoom.init.dropdowns.categoryCalendarDropdown,
        //     "Pilih Kategori",
        //     ""
        // );

        // await createDropdown(
        //     `${baseUrl}/utils/dropdowns/get-all-room-by-category?category=`,
        //     fnTransactionRoom.init.dropdowns.roomCalendarDropdown,
        //     "Pilih Kamar",
        //     ""
        // );
    },

    detailGuest: (id) => {
        window.open(`${baseUrl}/members/details/${id}`, "_blank");
    },

    onListMember: () => {
        fnTransactionRoom.init.modals.modalListMember.show();
    },

    detailRoom: async (slug) => {
        await fetch(`${baseUrl}/transactions/rent-rooms/detail-rooms/${slug}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        swal.fire(
                            "Terjadi kesalahan",
                            "Saat pengambilan detail kamar",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                fnTransactionRoom.init.modals.modalDetailTransactionRoom.show();

                document.querySelector("#detail-section").innerHTML = response;
            });
    },
};

fnTransactionRoom.onLoad();

viewRadio.forEach((item) => {
    item.addEventListener("click", () => {
        console.log(item);

        switch (item.value) {
            case "table-view":
                tableView.classList.remove("d-none");
                calendarView.classList.add("d-none");
                break;

            case "calendar-view":
                tableView.classList.add("d-none");
                calendarView.classList.remove("d-none");
                break;
        }
    });
});

fnTransactionRoom.init.dropdowns.categoryRoomDropdown.passedElement.element.addEventListener(
    "change",
    async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-all-room-by-category?category=${fnTransactionRoom.init.dropdowns.categoryCalendarDropdown.getValue(
                true
            )}`,
            fnTransactionRoom.init.dropdowns.roomCalendarDropdown,
            "Pilih Kamar",
            ""
        );
    }
);

fnTransactionRoom.init.dropdowns.roomCalendarDropdown.passedElement.element.addEventListener(
    "change",
    () => {
        room =
            fnTransactionRoom.init.dropdowns.roomCalendarDropdown.getValue(
                true
            );

        fnTransactionRoom.init.calendars.calendarFull.render();
    }
);

fnTransactionRoom.init.dropdowns.categoryRoomDropdown.passedElement.element.addEventListener(
    "change",
    () => {
        fnTransactionRoom.init.tables.tbRoom.ajax
            .url(
                `${baseUrl}/transactions/rent-rooms/get-all-data?category=${fnTransactionRoom.init.dropdowns.categoryRoomDropdown.getValue(
                    "true"
                )}`
            )
            .load();
    }
);
