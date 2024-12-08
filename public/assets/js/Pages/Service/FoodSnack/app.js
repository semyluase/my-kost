let startDate = moment(),
    endDate = moment();

const fnFoodSnack = {
    init: {
        buttons: {
            btnSearch: document.querySelector("#btn-search"),
        },
        litepicker: {
            transDate: new Litepicker({
                element: document.querySelector("#datepicker-icon"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                startDate: startDate,
                endDate: endDate,
                format: "DD/MM/YYYY",
                singleMode: false,
            }),
        },
        tables: {
            tbFoodSnack: $("#tb-food-snack").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/transactions/orders/food-snack/get-all-data?s=${startDate.format(
                        "YYYY-MM-DD"
                    )}&e${endDate.format("YYYY-MM-DD")}`,
                },
            }),
        },
    },
};

fnFoodSnack.init.buttons.btnSearch.addEventListener("click", () => {
    fnFoodSnack.init.tables.tbFoodSnack.ajax
        .url(
            `${baseUrl}/transactions/orders/food-snack/get-all-data?s=${moment(
                fnFoodSnack.init.litepicker.transDate.getStartDate()
            ).format("YYYY-MM-DD")}&e${moment(
                fnFoodSnack.init.litepicker.transDate.getEndDate()
            ).format("YYYY-MM-DD")}`
        )
        .load();
});
