const noHPInput = document.querySelector("#nomor-handphone");
const nameInput = document.querySelector("#name");
const nomorIdentitasInput = document.querySelector("#nomor-identitas");
const alamatInput = document.querySelector("#alamat");
const durasiInput = document.querySelectorAll("input[type=radio][name=durasi]");
const endRentDate = document.querySelector("#end-rent");

let url, data, method, durasi;

const fnTransaction = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
        dropdowns: {
            jenisIdentitasDropdown: new Choices(
                document.querySelector("#jenis-identitas")
            ),
        },
        datePicker: {
            dobPicker: new Litepicker({
                element: document.querySelector("#date-of-birth"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                format: "DD-MM-YYYY",
                singleMode: true,
                startDate: moment("01-01-1990", "DD-MM-YYYY").format(
                    "YYYY-MM-DD"
                ),
                lang: "id-ID",
            }),
            startRentPicker: new Litepicker({
                element: document.querySelector("#start-rent"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                format: "DD-MM-YYYY",
                singleMode: true,
                startDate: moment().format("YYYY-MM-DD"),
                lang: "id-ID",
            }),
        },
    },
};

durasiInput.forEach((item) => {
    item.addEventListener("click", () => {
        switch (item.value) {
            case "harian":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                )
                    .add(1, "day")
                    .format("DD-MM-YYYY");
                break;

            case "mingguan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                )
                    .add(1, "week")
                    .format("DD-MM-YYYY");
                break;

            case "bulanan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                )
                    .add(1, "month")
                    .format("DD-MM-YYYY");
                break;

            case "tahunan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                )
                    .add(1, "year")
                    .format("DD-MM-YYYY");
                break;
        }
    });
});

fnTransaction.init.buttons.btnSave.addEventListener("click", async () => {
    durasiInput.forEach((item) => {
        if (item.checked) {
            durasi = item.value;
        }
    });

    switch (nobukti) {
        case "":
            url = `${baseUrl}/transactions/rent-rooms`;

            data = JSON.stringify({
                noHP: noHPInput.value,
                name: nameInput.value,
                identity:
                    fnTransaction.init.dropdowns.jenisIdentitasDropdown.getValue(
                        true
                    ),
                identityNumber: nomorIdentitasInput.value,
                address: alamatInput.value,
                room: room,
                startRentDate: moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                ).format("YYYY-MM-DD"),
                dob: moment(
                    fnTransaction.init.datePicker.dobPicker.getDate.toJSDate
                ).format("YYYY-MM-DD"),
                durasi: durasi,
                _token: fnTransaction.init.buttons.btnSave.dataset.csrf,
            });

            method = "post";
            break;

        default:
            url = `${baseUrl}/transactions/rent-rooms/${nobukti}`;

            data = JSON.stringify({
                noHP: noHPInput.value,
                name: nameInput.value,
                identity:
                    fnTransaction.init.dropdowns.jenisIdentitasDropdown.getValue(
                        true
                    ),
                identityNumber: nomorIdentitasInput.value,
                address: alamatInput.value,
                room: room,
                startRentDate: moment(
                    fnTransaction.init.datePicker.startRentPicker.getDate
                        .toJSDate
                ).format("YYYY-MM-DD"),
                dob: moment(
                    fnTransaction.init.datePicker.dobPicker.getDate.toJSDate
                ).format("YYYY-MM-DD"),
                durasi: durasi,
                _token: fnTransaction.init.buttons.btnSave.dataset.csrf,
            });

            method = "update";
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
                    window.location.href = `${baseUrl}/transactions/rent-rooms`;
                }
            });
    } else {
        swalWithBootstrapButtons.fire(
            "Something wrong",
            results.data.message,
            "error"
        );
    }
});
