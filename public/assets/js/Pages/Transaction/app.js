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
            "",
            ""
        );
    },
};

fnTransactionRoom.onLoad();
