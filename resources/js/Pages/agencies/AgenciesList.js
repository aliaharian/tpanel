import React, { useState, useEffect } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";

export default function AgenciesList(props) {
    const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
    const [selectedService, setSelectedService] = useState(null);
    const confirmDeleteService = (item) => {
        setSelectedService(item);
        setOpenDeleteDialog(true);
    };
    const handleDeleteService = () => {
        Inertia.delete(route("agencies.destroy", selectedService.id));
    };
    const handleEditService = (item) => {
        Inertia.get(route("agencies.edit", item.id));
    };

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    لیست آژانس ها
                </h2>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openDeleteDialog}
                setOpen={setOpenDeleteDialog}
                title="مطمئن هستید؟"
                text={`با این کار آژانس ${selectedService?.agency_name} برای همیشه حذف خواهد شد`}
                handleDoAction={handleDeleteService}
            />

            {props.flash.error && (
                <div
                    className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert"
                >
                    <strong className="font-bold"> خطا! </strong>
                    <span className="block sm:inline">{props.flash.error}</span>
                </div>
            )}
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="flex flex-col">
                            <div className="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div className="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                                    <div className="overflow-hidden">
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
                                                        نام آژانس
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
                                                {props.agencies.map(
                                                    (item, index) => (
                                                        <tr className="bg-white border-b">
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {index + 1}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {
                                                                    item.agency_name
                                                                }
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() =>
                                                                        handleEditService(
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
                                                                        confirmDeleteService(
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
