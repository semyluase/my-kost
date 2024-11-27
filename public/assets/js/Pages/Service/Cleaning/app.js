let startDate = moment();

const fnCleaning = {
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
                format: "DD/MM/YYYY",
                singleMode: true,
            }),
        },
        tables: {
            tbCleaning: $("#tb-cleaning").DataTable({
                processing: true,
                ajax: {
                    url: `${baseUrl}/transactions/orders/cleaning/get-all-data?tgl=${startDate.format(
                        "YYYY-MM-DD"
                    )}`,
                },
            }),
        },
    },

    onStartCleaning: async (nobukti, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/transactions/orders/cleaning/start`,
            JSON.stringify({
                nobukti: nobukti,
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swal.fire("Berhasil", results.data.message, "success").then(
                (result) => {
                    if (result.isConfirmed) {
                        fnCleaning.init.tables.tbCleaning.ajax
                            .url(
                                `${baseUrl}/transactions/orders/cleaning/get-all-data?tgl=${moment(
                                    fnCleaning.init.litepicker.transDate.getDate()
                                ).format("YYYY-MM-DD")}`
                            )
                            .load();
                    }
                }
            );
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
        }
    },

    onStopCleaning: async (nobukti, csrf) => {
        blockUI();

        const results = await onSaveJson(
            `${baseUrl}/transactions/orders/cleaning/stop`,
            JSON.stringify({
                nobukti: nobukti,
                _token: csrf,
            }),
            "post"
        );

        unBlockUI();

        if (results.data.status) {
            swal.fire("Berhasil".results.data.message, "success").then(
                (result) => {
                    if (result.isConfirmed) {
                        fnCleaning.init.tables.tbCleaning.ajax
                            .url(
                                `${baseUrl}/transactions/orders/cleaning/get-all-data?tgl=${moment(
                                    fnCleaning.init.litepicker.transDate.getDate()
                                ).format("YYYY-MM-DD")}`
                            )
                            .load();
                    }
                }
            );
        } else {
            swal.fire("Terjadi kesalahan", results.data.message, "error");
        }
    },
};

fnCleaning.init.buttons.btnSeacrh.addEventListener("click", () => {
    fnCleaning.init.tables.tbCleaning.ajax
        .url(
            `${baseUrl}/transactions/orders/cleaning/get-all-data?s=${moment(
                fnCleaning.init.litepicker.transDate.getDate()
            ).format("YYYY-MM-DD")}`
        )
        .load();
});

