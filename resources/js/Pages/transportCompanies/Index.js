import React, { useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";

export default function TransportCompaniesList(props) {
    const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
    const [selectedCompany, setSelectedCompany] = useState(null);
    const confirmDeleteCompany = (item) => {
        setSelectedCompany(item);
        setOpenDeleteDialog(true);
    };
    const handleDeleteCompany = () => {
        Inertia.delete(route("transportCompanies.destroy", selectedCompany.id));
    };
    const handleEditCompany = (item) => {
        Inertia.get(route("transportCompanies.edit", item.id));
    };
    const handleActiveCompany = (item) => {
        Inertia.post(route("transportCompanies.active", item.id));
    };
    const renderTransport = (item) => {
        switch (item) {
            case "AIRPLANE":
                return "هواپیما";
            case "TRAIN":
                return "قطار";
            case "BUS":
                return "اتوبوس";
        }
    };
    console.log(props);
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    لیست شرکت های حمل و نقل
                </h2>
            }
            action={
                <a
                    href={route("transportCompanies.create")}
                    className="bg-blue-300 py-3 px-6 rounded-xl hover:bg-blue-500 text-white"
                >
                    ایجاد شرکت
                </a>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openDeleteDialog}
                setOpen={setOpenDeleteDialog}
                title="مطمئن هستید؟"
                text={`با این کار این شرکت حمل و نقل و تمام موارد مربوط به آن برای همیشه حذف خواهد شد`}
                handleDoAction={handleDeleteCompany}
            />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="flex flex-col">
                            <div className="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div className="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                                    <div className="overflow-hidden">
                                        {props.flash?.success && (
                                            <Alert
                                                color="green"
                                                text={props.flash?.success}
                                            />
                                        )}

                                        <table className="min-w-full text-center">
                                            <thead className="border-b bg-gray-50">
                                                <tr>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        #
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        لوگو
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        نام
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        نوع
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        وضعیت
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        ویرایش
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        حذف
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {props.companies.map(
                                                    (item, index) => (
                                                        <tr
                                                            key={index}
                                                            className="bg-white border-b"
                                                        >
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {index + 1}
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                <img
                                                                    style={{
                                                                        height: 30,
                                                                        margin: "0 auto",
                                                                    }}
                                                                    src={
                                                                        item
                                                                            .logo
                                                                            ?.url
                                                                    }
                                                                />
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {item.name}
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {renderTransport(
                                                                    item.transport_type
                                                                )}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() =>
                                                                        handleActiveCompany(
                                                                            item
                                                                        )
                                                                    }
                                                                    className={`${
                                                                        item.active ==
                                                                        1
                                                                            ? "bg-green-500"
                                                                            : "bg-red-500"
                                                                    } text-white font-bold py-2 px-4 rounded-full cursor-pointer`}
                                                                >
                                                                    {item.active ==
                                                                    1
                                                                        ? "فعال"
                                                                        : "غیر فعال"}
                                                                </a>
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() =>
                                                                        handleEditCompany(
                                                                            item
                                                                        )
                                                                    }
                                                                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full cursor-pointer"
                                                                >
                                                                    ویرایش
                                                                </a>
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() =>
                                                                        confirmDeleteCompany(
                                                                            item
                                                                        )
                                                                    }
                                                                    className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full cursor-pointer"
                                                                >
                                                                    حذف
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    )
                                                )}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Authenticated>
    );
}
