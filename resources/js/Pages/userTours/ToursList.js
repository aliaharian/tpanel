import React, { useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";
import moment from "jalali-moment";

export default function ToursList(props) {
    const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
    const [selectedWatcher, setSelectedWatcher] = useState(null);
    const confirmDeleteWatcher = (item) => {
        setSelectedWatcher(item);
        setOpenDeleteDialog(true);
    };
    const handleDeleteWatcher = () => {
        Inertia.post(route("userTour.fail", selectedWatcher.id));
    };
    const handleEditWatcher = (item) => {
        Inertia.get(route("userTour.edit", item.id));
    };
    const timetoJalali = (time) => {
        moment.locale("fa", { useGregorianParser: true });

        return moment(time).format("jYYYY/jMM/jDD");
    };
    console.log(props.watchers);
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    لیست تور های رزرو شده
                </h2>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openDeleteDialog}
                setOpen={setOpenDeleteDialog}
                title="مطمئن هستید؟"
                actionText="لغو"
                text={`با این کار این تور لغو خواهد شد و مبلغ آن به کیف پول کاربر واریز میگردد`}
                handleDoAction={handleDeleteWatcher}
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
                                                        عنوان تور
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        وضعیت تور
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
                                                        لغو
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {props.tours.map(
                                                    (item, index) => (
                                                        <tr
                                                            key={index}
                                                            className="bg-white border-b"
                                                        >
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {index + 1}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {item.agency
                                                                    ? "آژانس"
                                                                    : "کاربر"}
                                                                :{" "}
                                                                {item.agency
                                                                    ? item
                                                                          .agency
                                                                          .agency_name
                                                                    : item.user
                                                                          .name +
                                                                      " " +
                                                                      item.user
                                                                          .last_name}{" "}
                                                                - از{" "}
                                                                {
                                                                    item
                                                                        .from_city
                                                                        .title
                                                                }{" "}
                                                                به{" "}
                                                                {
                                                                    item.to_city
                                                                        .title
                                                                }
                                                                - از تاریخ{" "}
                                                                {timetoJalali(
                                                                    parseInt(
                                                                        item
                                                                            .departure_vehicle
                                                                            ?.departure_date_time
                                                                    )
                                                                )}{" "}
                                                                تا تاریخ{" "}
                                                                {timetoJalali(
                                                                    parseInt(
                                                                        item
                                                                            .arrival_vehicle
                                                                            ?.departure_date_time
                                                                    )
                                                                )}{" "}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {
                                                                    item.status
                                                                        ?.slug
                                                                }
                                                            </td>

                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {item.status
                                                                    .value !==
                                                                    "FAILED" && (
                                                                    <a
                                                                        onClick={() =>
                                                                            handleEditWatcher(
                                                                                item
                                                                            )
                                                                        }
                                                                        className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full cursor-pointer"
                                                                    >
                                                                        ویرایش
                                                                    </a>
                                                                )}
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                {item.status
                                                                    .value !==
                                                                    "FAILED" && (
                                                                    <a
                                                                        onClick={() =>
                                                                            confirmDeleteWatcher(
                                                                                item
                                                                            )
                                                                        }
                                                                        className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full cursor-pointer"
                                                                    >
                                                                        لغو
                                                                    </a>
                                                                )}
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
