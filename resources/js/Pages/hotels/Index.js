import React, { useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";
import moment from "jalali-moment";

export default function TransportCompaniesList(props) {
    const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
    const [selectedHotel, setSelectedHotel] = useState(null);
    const confirmDeleteHotel = (item) => {
        setSelectedHotel(item);
        setOpenDeleteDialog(true);
    };
    const handleDeleteHotel = () => {
        Inertia.delete(route("hotels.destroy", selectedHotel.id));
    };
    const handleEditHotel = (item) => {
        Inertia.get(route("hotels.edit", item.id));
    };
    const timetoJalali = (time) => {
        moment.locale("fa", { useGregorianParser: true });

        return moment(time).format("jYYYY/jMM/jDD");
    };
    const handleActiveHotel = (item) => {
        Inertia.post(route("hotels.active", item.id));
    };
    const renderHotel = (item) => {
        switch (item) {
            case "hotel":
                return "هتل";
            case "apartment":
                return "اپارتمان";
            case "hotelApartment":
                return "هتل آپارتمان";
        }
    };
    console.log(props);

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    لیست هتل ها
                </h2>
            }
            action={
                <a
                    href={route("hotels.create")}
                    className="bg-blue-300 py-3 px-6 rounded-xl hover:bg-blue-500 text-white"
                >
                    ایجاد هتل
                </a>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openDeleteDialog}
                setOpen={setOpenDeleteDialog}
                title="مطمئن هستید؟"
                text={`با این کار این هتل و تمام موارد مربوط به آن برای همیشه حذف خواهد شد`}
                handleDoAction={handleDeleteHotel}
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
                                                        شهر
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        تاریخ از
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        تاریخ تا
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        موجودی
                                                    </th>

                                                    <th
                                                        scope="col"
                                                        className="text-sm font-medium text-gray-900 px-6 py-4"
                                                    >
                                                        گالری
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
                                                {props.hotels.map(
                                                    (item, index) => (
                                                        <tr
                                                            key={index}
                                                            className="bg-white border-b"
                                                        >
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {index + 1}
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {item.name}
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {renderHotel(
                                                                    item.type
                                                                )}
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {
                                                                    item
                                                                        .city_place
                                                                        .title
                                                                }
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                <p
                                                                    className={
                                                                        item.available_time_from >
                                                                        Date.now()
                                                                            ? "text-red-500"
                                                                            : ""
                                                                    }
                                                                >
                                                                    {timetoJalali(
                                                                        parseInt(
                                                                            item.available_time_from
                                                                        )
                                                                    )}
                                                                </p>
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                <p
                                                                    className={
                                                                        item.available_time_to <
                                                                        Date.now()
                                                                            ? "text-red-500"
                                                                            : ""
                                                                    }
                                                                >
                                                                    {timetoJalali(
                                                                        parseInt(
                                                                            item.available_time_to
                                                                        )
                                                                    )}
                                                                </p>
                                                            </td>
                                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                <p
                                                                    className={
                                                                        parseInt(
                                                                            item.capacity
                                                                        ) -
                                                                            item.used_capacity <
                                                                        5
                                                                            ? "text-red-500"
                                                                            : ""
                                                                    }
                                                                >
                                                                    {parseInt(
                                                                        item.capacity
                                                                    ) -
                                                                        item.used_capacity}
                                                                </p>
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() => {
                                                                        Inertia.get(
                                                                            route(
                                                                                "hotels.gallery",
                                                                                item.id
                                                                            )
                                                                        );
                                                                    }}
                                                                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full cursor-pointer"
                                                                >
                                                                    گالری
                                                                </a>
                                                            </td>
                                                            <td className="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                                <a
                                                                    onClick={() =>
                                                                        handleActiveHotel(
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
                                                                        handleEditHotel(
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
                                                                        confirmDeleteHotel(
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
