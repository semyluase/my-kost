const fnTransactionRoom = {
    init: {
        dropdowns: {
            categoryRoomDropdown: new Choices(
                document.querySelector("#category-room")
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
