const fnTransactionRoom = {
    init: {
        buttons: {
            btnListMember: document.querySelector("#btn-list-member"),
        },
        dropdowns: {
            categoryRoomDropdown: new Choices(
                document.querySelector("#category-room"),
                { shouldSort: false }
            ),
        },
        modals: {
            modalListMember: new bootstrap.Modal(
                document.querySelector("#modal-list-member")
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
    },

    detailGuest: (id) => {
        window.open(`${baseUrl}/members/details/${id}`, "_blank");
    },

    onListMember: () => {
        fnTransactionRoom.init.modals.modalListMember.show();
    },
};

fnTransactionRoom.onLoad();

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
