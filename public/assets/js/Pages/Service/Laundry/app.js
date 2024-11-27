let startDate = moment().startOf("month"),
    endDate = moment();

const fnLaundry = {
    init: {
        buttons: {
            btnSeacrh: document.querySelector("#btn-search"),
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
            tbLaundry: $("#tb-laundry").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/transactions/orders/laundry/get-all-data?s=${startDate.format(
                        "YYYY-MM-DD"
                    )}&e${endDate.format("YYYY-MM-DD")}`,
                },
            }),
        },
    },

    onTakeLaundry: (nobukti) => {
        window.location.href = `${baseUrl}/transactions/orders/laundry/create?nobukti=${nobukti}`;
    },
};

fnLaundry.init.buttons.btnSeacrh.addEventListener("click", () => {
    fnLaundry.init.tables.tbLaundry.ajax
        .url(
            `${baseUrl}/transactions/orders/laundry/get-all-data?s=${moment(
                fnLaundry.init.litepicker.transDate.getStartDate()
            ).format("YYYY-MM-DD")}&e${moment(
                fnLaundry.init.litepicker.transDate.getEndDate()
            ).format("YYYY-MM-DD")}`
        )
        .load();
});
