const kodeItemInput = document.querySelector("#kode-item");
const hargaInput = document.querySelector("#price");

const fnPriceCleaning = {
    init: {
        buttons: {
            btnAdd: document.querySelector("#btn-add-price-cleaning"),
            btnSave: document.querySelector("#btn-save-price-cleaning"),
        },
        dropdowns: {
            categoryDropdown: new Choices(document.querySelector("#category")),
        },
        modals: {
            priceCleaningModal: new bootstrap.Modal(
                document.querySelector("#modal-price-cleaning")
            ),
        },
        tables: {
            tbPriceCleaning: $("#tb-price-cleaning").DataTable({
                ajax: {
                    url: `${baseUrl}/masters/cleaning-price/get-all-data`,
                },
                processing: true,
                serverSide: true,
                ordering: false,
                scrollX: true,
            }),
        },
    },
};
