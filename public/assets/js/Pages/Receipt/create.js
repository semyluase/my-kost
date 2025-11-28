let url,
    data,
    method,
    startDate = moment().startOf("month"),
    endDate = moment();

const fnReceipt = {
    init: {
        buttons: {
            btnBuatLaporan: document.querySelector("#btn-buat-laporan"),
            btnGenerateReport: document.querySelector("#btn-generate-report"),
        },
        dropdowns: {
            barangReportDropdown: new Choices(
                document.querySelector("#barang-report"),
                {
                    shouldSort: false,
                }
            ),
            identitasKosReportDropdown: new Choices(
                document.querySelector("#identitas-kos"),
                {
                    shouldSort: false,
                }
            ),
        },
        modals: {
            modalGenerataReport: new bootstrap.Modal(
                document.querySelector("#modal-report")
            ),
        },
        litepicker: {
            tanggalReport: new Litepicker({
                element: document.querySelector("#tanggal-report"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                startDate: startDate.format("DD/MM/YYYY"),
                endDate: endDate.format("DD/MM/YYYY"),
                format: "DD/MM/YYYY",
                singleMode: false,
            }),
        },
    },
};

fnReceipt.init.buttons.btnBuatLaporan.addEventListener("click", async () => {
    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-items`,
        fnReceipt.init.dropdowns.barangReportDropdown,
        "",
        ""
    );

    await createDropdown(
        `${baseUrl}/utils/dropdowns/get-homes`,
        fnReceipt.init.dropdowns.identitasKosReportDropdown,
        "",
        parseInt(identitasKos)
    );

    if (lockLocation == 1) {
        fnReceipt.init.dropdowns.identitasKosReportDropdown.disable();
    } else {
        fnReceipt.init.dropdowns.identitasKosReportDropdown.enable();
    }
    fnReceipt.init.modals.modalGenerataReport.show();
});

fnReceipt.init.buttons.btnGenerateReport.addEventListener("click", () => {
    fnReceipt.init.modals.modalGenerataReport.hide();

    window.open(
        `${baseUrl}/inventories/receipts/generateReport?s=${moment(
            fnReceipt.init.litepicker.tanggalReport.getStartDate().toJSDate()
        ).format("YYYY-MM-DD")}&e=${moment(
            fnReceipt.init.litepicker.tanggalReport.getEndDate().toJSDate()
        ).format(
            "YYYY-MM-DD"
        )}&item=${fnReceipt.init.dropdowns.barangReportDropdown.getValue(
            true
        )}&home=${fnReceipt.init.dropdowns.identitasKosReportDropdown.getValue(
            true
        )}`,
        "_blank"
    );
});
