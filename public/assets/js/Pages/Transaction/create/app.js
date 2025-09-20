const noHPInput = document.querySelector("#nomor-handphone");
const nameInput = document.querySelector("#name");
const emailInput = document.querySelector("#email");
const nomorIdentitasInput = document.querySelector("#nomor-identitas");
const alamatInput = document.querySelector("#alamat");
const durasiInput = document.querySelectorAll("input[type=radio][name=durasi]");
const endRentDate = document.querySelector("#end-rent");
const uploadIdentity = document.querySelector("#upload-identity");
const showIdentity = document.querySelector("#show-identity");
const imgIdentity = document.querySelector("#image-identity");
const uploadFotoOrang = document.querySelector("#upload-foto-orang");
const showFotoOrang = document.querySelector("#show-foto-orang");
const imgOrang = document.querySelector("#image-orang");

let url,
    data,
    method,
    durasi,
    uploaded,
    tokenFoto,
    tokenFotoOrang,
    startDateRent = moment(),
    tglLahir = moment("1990-01-01");

const fnTransaction = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
            btnViewMember: document.querySelector("#btn-view-member"),
        },
        dropdowns: {
            jenisIdentitasDropdown: new Choices(
                document.querySelector("#jenis-identitas")
            ),
        },
        datePicker: {
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
                startDate: startDateRent,
                lang: "id-ID",
            }),
            tanggalLahir: new Litepicker({
                element: document.querySelector("#tanggal-lahir"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                },
                format: "DD/MM/YYYY",
                singleMode: true,
                startDate: tglLahir,
                lang: "id-ID",
            }),
        },
        dropzones: {
            dropzoneFotoIdentitas: new Dropzone("#dropzone-foto", {
                url: `${baseUrl}/transactions/rent-rooms/upload-identity`,
                maxFiles: 1,
                method: "post",
                paramName: "userfile",
                addRemoveLinks: true,
                success: function (file, response) {
                    // var data = JSON.parse(response);
                    tokenFoto = response.token;
                    console.log(tokenFoto);
                },
            }),
            dropzoneFotoOrang: new Dropzone("#dropzone-foto-orang", {
                url: `${baseUrl}/transactions/rent-rooms/upload-foto-orang`,
                maxFiles: 1,
                method: "post",
                paramName: "userfileFoto",
                addRemoveLinks: true,
                success: function (file, response) {
                    // var data = JSON.parse(response);
                    tokenFotoOrang = response.token;
                    console.log(tokenFotoOrang);
                },
            }),
        },
        modals: {
            modalViewMember: new bootstrap.Modal(
                document.querySelector("#modal-view-member")
            ),
        },
        tables: {
            tbViewMember: $("#tb-view-member").DataTable({
                ajax: {
                    url: `${baseUrl}/utils/master/user`,
                },
                processing: true,
            }),
        },
    },

    onSelectMember: async (noHp) => {
        noHPInput.value = noHp;
        fnTransaction.init.modals.modalViewMember.hide();
        await fnTransaction.searchMember(noHp);
    },

    searchMember: async (noHP) => {
        blockUI();

        await fetch(
            `${baseUrl}/transactions/rent-rooms/search-member?phoneNumber=${noHP}`
        )
            .then((response) => {
                if (!response.ok) {
                    unBlockUI();

                    throw new Error(
                        swal.fire(
                            `Terjadi kesalahan`,
                            "Saat mengambil data",
                            "error"
                        )
                    );
                }

                return response.json();
            })
            .then((response) => {
                unBlockUI();
                if (response.id) {
                    nameInput.value = response.name;
                    fnTransaction.init.dropdowns.jenisIdentitasDropdown.setChoiceByValue(
                        response.member == null
                            ? ""
                            : response.member.type_identity
                    );
                    nomorIdentitasInput.value = response.member.identity;
                    let dobMember = "1990-01-01";
                    if (response.member) {
                        if (response.member.dob) {
                            dobMember = response.member.dob;
                        }
                    }
                    fnTransaction.init.datePicker.tanggalLahir.setDate(
                        dobMember
                    );
                    tokenFoto = response.member
                        ? response.member.user_identity
                            ? response.member.user_identity.token
                            : ""
                        : "";

                    tokenFotoOrang = response.foto ? response.foto.token : "";

                    console.log(response.member);

                    if (response.member) {
                        if (response.member.user_identity) {
                            uploadIdentity.classList.add("d-none");
                            showIdentity.classList.remove("d-none");
                            imgIdentity.src = `${baseUrl}/assets/upload/userIdentity/${response.member.user_identity.file_name}`;
                        }
                    }

                    if (response.foto) {
                        uploadFotoOrang.classList.add("d-none");
                        showFotoOrang.classList.remove("d-none");
                        imgOrang.src = `${baseUrl}/assets/upload/userIdentity/${response.foto.file_name}`;
                    }
                } else {
                    nameInput.value = "";
                    nomorIdentitasInput.value = "";
                }

                unBlockUI();
            });
    },
};

fnTransaction.init.buttons.btnViewMember.addEventListener("click", () => {
    fnTransaction.init.modals.modalViewMember.show();
});

noHPInput.addEventListener("keyup", async (event) => {
    if (event.key == "Enter") {
        await fnTransaction.searchMember(noHPInput.value);
    }
});

durasiInput.forEach((item) => {
    item.addEventListener("click", () => {
        switch (item.value) {
            case "harian":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
                )
                    .add(1, "day")
                    .format("DD-MM-YYYY");
                break;

            case "mingguan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
                )
                    .add(1, "week")
                    .format("DD-MM-YYYY");
                break;

            case "bulanan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
                )
                    .add(1, "month")
                    .format("DD-MM-YYYY");
                break;

            case "tahunan":
                endRentDate.value = moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
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
                email: emailInput.value,
                tanggalLahir: moment(
                    fnTransaction.init.datePicker.tanggalLahir
                        .getDate()
                        .toJSDate()
                ).format("YYYY-MM-DD"),
                identity:
                    fnTransaction.init.dropdowns.jenisIdentitasDropdown.getValue(
                        true
                    ),
                identityNumber: nomorIdentitasInput.value,
                tokenFoto: tokenFoto,
                tokenFotoOrang: tokenFotoOrang,
                room: room,
                startRentDate: moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
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
                email: emailInput.value,
                tanggalLahir: moment(
                    fnTransaction.init.datePicker.tanggalLahir
                        .getDate()
                        .toJSDate()
                ).format("YYYY-MM-DD"),
                identity:
                    fnTransaction.init.dropdowns.jenisIdentitasDropdown.getValue(
                        true
                    ),
                identityNumber: nomorIdentitasInput.value,
                tokenFoto: tokenFoto,
                tokenFotoOrang: tokenFotoOrang,
                room: room,
                startRentDate: moment(
                    fnTransaction.init.datePicker.startRentPicker
                        .getDate()
                        .toJSDate()
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
        window.location.href = `${baseUrl}/transactions/rent-rooms/detail-rents/${results.data.noKamar}`;
    } else {
        swalWithBootstrapButtons.fire(
            "Something wrong",
            results.data.message,
            "error"
        );
    }
});
