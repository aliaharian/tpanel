import React, { useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";

export default function RoomServicesList(props) {
    const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
    const [selectedService, setSelectedService] = useState(null);
    const confirmDeleteService = (item) => {
        setSelectedService(item);
        setOpenDeleteDialog(true);
    };
    const handleDeleteService = () => {
        Inertia.delete(route("roomServices.destroy", selectedService.id));
    };
    const handleEditService = (item) => {
        Inertia.get(route("roomServices.edit", item.id));
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    لیست خدمات اتاق
                </h2>
            }
            action={
                <a
                    href={route("roomServices.create")}
                    className="bg-blue-400 py-3 px-6 rounded-xl hover:bg-blue-500 text-white"
                >
                    ایجاد سرویس اتاق
                </a>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openDeleteDialog}
                setOpen={setOpenDeleteDialog}
                title="مطمئن هستید؟"
                text={`با این کار خدمت ${selectedService?.name} برای همیشه حذف خواهد شد`}
                handleDoAction={handleDeleteService}
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
                                                        نام خدمت
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
                                                {props.services.map(
                                                    (item, index) => (
                                                        <tr className="bg-white border-b">
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {index + 1}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {item.name}
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
