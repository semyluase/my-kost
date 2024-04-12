const roleChoices = document.querySelector("#role");

const fnRoleMenu = {
    init: {
        buttons: {
            btnSave: document.querySelector("#btn-save"),
        },
        dropdowns: {
            roleDropdown: new Choices(document.querySelector("#role")),
        },
    },

    onLoad: async () => {
        await createDropdown(
            `${baseUrl}/utils/dropdowns/get-roles`,
            fnRoleMenu.init.dropdowns.roleDropdown,
            "Pilih Role",
            ""
        );

        if (!fnRoleMenu.init.buttons.btnSave.classList.contains("d-none")) {
            fnRoleMenu.init.buttons.btnSave.classList.add("d-none");
        }
    },
};

fnRoleMenu.onLoad();

roleChoices.addEventListener("change", async () => {
    blockUI();

    await fetch(
        `${baseUrl}/settings/role-menus/get-menu-data?r=${fnRoleMenu.init.dropdowns.roleDropdown.getValue(
            "true"
        )}`
    )
        .then((response) => {
            if (!response.ok) {
                unBlockUI();
                throw new Error(
                    swalWithBootstrapButtons.fire(
                        "Terjadi kesalahan",
                        "Saat mengambil data",
                        "error"
                    )
                );
            }

            return response.json();
        })
        .then((response) => {
            unBlockUI();
            $("#list-menu").jstree("destroy");
            $("#list-menu").jstree({
                plugins: ["wholerow", "checkbox", "types"],
                core: {
                    themes: {
                        responsive: !1,
                    },
                    data: response,
                },
                types: {
                    default: {
                        icon: "",
                    },
                },
            });

            if (fnRoleMenu.init.buttons.btnSave.classList.contains("d-none")) {
                fnRoleMenu.init.buttons.btnSave.classList.remove("d-none");
            }
        });
});

fnRoleMenu.init.buttons.btnSave.addEventListener("click", async () => {
    blockUI();

    let arr = $("#list-menu").jstree("get_checked");
    $("#list-menu")
        .find(".jstree-undetermined")
        .each(function (i, element) {
            arr.push($(element).closest(".jstree-node").attr("id"));
        });

    const results = await onSaveJson(
        `${baseUrl}/settings/role-menus`,
        JSON.stringify({
            menu: arr,
            role: fnRoleMenu.init.dropdowns.roleDropdown.getValue(true),
            _token: fnRoleMenu.init.buttons.btnSave.dataset.csrf,
        }),
        "post"
    );

    unBlockUI();

    if (results.data.status) {
        swalWithBootstrapButtons
            .fire("Berhasil", results.data.message, "success")
            .then(async (result) => {
                if (result.isConfirmed) {
                    $("#list-menu").jstree("destroy");
                    await fnRoleMenu.onLoad();
                }
            });
    } else {
        if (typeof results.data.message == "string") {
            swalWithBootstrapButtons.fire(
                "Terjadi kesalahan",
                results.data.message,
                "error"
            );

            return false;
        }
    }
});
