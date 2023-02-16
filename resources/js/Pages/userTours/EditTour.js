import React, { useEffect, useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";
import moment from "jalali-moment";
import ValidationErrors from "@/Components/ValidationErrors";
import Label from "@/Components/Label";
import Select from "@/Components/Select";
import { DatePicker } from "jalali-react-datepicker";
import axios from "axios";
import Radio from "@/Components/Radio";
import Checkbox from "@/Components/Checkbox";
import TourRoom from "../watchers/TourRoom";
import Button from "@/Components/Button";

export default function EditTour(props) {
    const { data, setData, post, put, processing, errors, setErrors, reset } =
        useForm({
            hotel: props.tour?.hotel_id,
            departure_date_time: props.tour?.departure_date_time,
            arrival_date_time: props.tour?.arrival_date_time,
            departure_transport_type:
                props.tour?.departure_vehicle.transport_type,
            arrival_transport_type: props.tour?.arrival_vehicle.transport_type,
            departure_transport_vehicle: props.tour?.departure_vehicle_id,
            arrival_transport_vehicle: props.tour?.arrival_vehicle_id,
            fullboard: props.tour?.fullboard || 0,
            services:
                props.tour?.services.map((item) => item.id.toString()) || [],
            rooms: props.tour?.rooms || [
                {
                    id: 1,
                    name: "",
                    capacity: 0,
                },
            ],
        });
    console.log(props.tour);
    console.log("data",data);
    const [hotels, setHotels] = useState(props.hotels);
    const [totalPrice, setTotalPrice] = useState(props.tour?.payablePrice || 0);
    const [clientErrors, setClientErrors] = useState([]);
    const [fromVehicles, setFromVehicles] = useState(props.departure_vehicles);
    const [toVehicles, setToVehicles] = useState(props.arrival_vehicles);
    //confirmText state
    const [confirmText, setConfirmText] = useState("");
    //openConfirmDialog
    const [openConfirmDialog, setOpenConfirmDialog] = useState(false);
    //add room
    const addRoom = () => {
        setData("rooms", [
            ...data.rooms,
            {
                id: data.rooms.length + 1,
                name: "",
                capacity: 0,
            },
        ]);
    };
    //remove room
    const removeRoom = (id) => {
        setData(
            "rooms",
            data.rooms.filter((item) => item.id !== id)
        );
    };

    const onHandleChange = (event) => {
        // console.log(typeof event);

        if (event.target.type === "checkbox") {
            let tmp = data[event.target.name];
            if (tmp.indexOf(event.target.value) === -1) {
                tmp = [...tmp, event.target.value];
            } else {
                tmp.splice(tmp.indexOf(event.target.value), 1);
            }
            setData(event.target.name, [...tmp]);
        } else if (event.target.type === "file") {
            // console.log(event.target.files[0]);
            setData(event.target.name, event.target.files[0]);
        } else {
            setData(event.target.name, event.target.value);
        }
    };

    const onHandleChangeTimeFrom = ({ value }) => {
        let val = Math.floor(value);
        setData("departure_date_time", val);
        getFreeHotels(val);
        getFreeDepartureVehicle(null, val, null);
        getFreeArrivalVehicle(null, val, null);
        // setData("hotel", null);
        // setData("departure_transport_vehicle", null);
        // setData("arrival_transport_vehicle", null);
    };
    const onHandleChangeTimeTo = ({ value }) => {
        let val = Math.floor(value);
        setData("arrival_date_time", val);
        getFreeHotels(null, val);
        getFreeDepartureVehicle(null, null, val);
        getFreeArrivalVehicle(null, null, val);
        // setData("hotel", null);
        // setData("departure_transport_vehicle", null);
        // setData("arrival_transport_vehicle", null);
    };
    const onHandleChangeDepartureVehicle = (e) => {
        setData(e.target.name, e.target.value);
        getFreeDepartureVehicle(e.target.value);
        // setData("departure_transport_vehicle", null);
    };

    const onHandleChangeArrivalVehicle = (e) => {
        setData(e.target.name, e.target.value);
        getFreeArrivalVehicle(e.target.value);
        // setData("arrival_transport_vehicle", null);
    };
    const getFreeDepartureVehicle = async (type, from, to) => {
        let response = await axios.post(
            route("userTour.freeDepartureVehicles"),
            {
                from: from || data.departure_date_time,
                to: to || data.arrival_date_time,
                from_city: props.tour.from_city_id,
                to_city: props.tour.to_city_id,
                type: type || data.departure_transport_type,
                hotel: data.hotel,
                adult: props.tour.adult_count,
                teens: props.tour.teen_count,
                kids: props.tour.kid_count,
                infants: props.tour.infant_count,
            }
        );

        setFromVehicles(response.data);
    };
    const getFreeArrivalVehicle = async (type, from, to) => {
        let response = await axios.post(route("userTour.freeArrivalVehicles"), {
            from: from || data.departure_date_time,
            to: to || data.arrival_date_time,
            from_city: props.tour.from_city_id,
            to_city: props.tour.to_city_id,
            type: type || data.departure_transport_type,
            hotel: data.hotel,
            adult: props.tour.adult_count,
            teens: props.tour.teen_count,
            kids: props.tour.kid_count,
            infants: props.tour.infant_count,
        });

        setToVehicles(response.data);
    };

    const getFreeHotels = async (from, to) => {
        // console.log(event.target.value);
        let response = await axios.post(route("userTour.freeHotels"), {
            city: props.tour.to_city_id,
            from: from || data.departure_date_time,
            to: to || data.arrival_date_time,
            adult: props.tour.adult_count,
            teens: props.tour.teen_count,
            kids: props.tour.kid_count,
            infants: props.tour.infant_count,
        });

        setHotels(response.data);
    };
    const renderVehicleName = (item) => {
        moment.locale("fa", { useGregorianParser: true });

        let date = moment(parseInt(item.departure_date_time)).format(
            "jYYYY/jMM/jDD HH:mm"
        );
        console.log("date", date);

        return `${item.name} حرکت تاریخ ${date}
        `;
    };
    //changeRoomName by object id
    const changeRoomName = (id, e) => {
        console.log("room", e.target.value);

        let room = data.rooms.find((item) => item.id === id);
        room.name = e.target.value;
        setData("rooms", [...data.rooms]);
    };
    //changeRoomName by object id
    const changeRoomCapacity = (id, e) => {
        let room = data.rooms.find((item) => item.id === id);
        console.log("room", e.target.value);

        room.capacity = e.target.value;
        setData("rooms", [...data.rooms]);
    };

    //useEffect on data to calculate price
    useEffect(async () => {
        let response = await axios.post(route("userTour.calculatePrice"), {
            from_date: data.departure_date_time,
            to_date: data.arrival_date_time,
            from_city: props.tour.from_city_id,
            to_city: props.tour.to_city_id,
            from_vehicle: data.departure_transport_vehicle,
            to_vehicle: data.arrival_transport_vehicle,
            hotel: data.hotel,
            adult: props.tour.adult_count,
            teens: props.tour.teen_count,
            kids: props.tour.kid_count,
            infants: props.tour.infant_count,
            fullboard: data.fullboard,
            services:
                data.services.length > 0
                    ? data.services.map((item) => item).join(",")
                    : null,
        });
        console.log(response.data);
        setTotalPrice(response.data.payable_price_format);
    }, [data]);

    const handleProcess = async (e) => {
        let flag = true;
        //check if rooms array not empty
        //clear client errors
        setClientErrors([]);
        if (data.rooms.length === 0) {
            //set inertia form error
            setClientErrors(["لطفا حداقل یک اتاق اضافه کنید"]);
            window.scrollTo(0, 0);
            flag = false;
        }
        //check if hotels is empty
        if (hotels.length === 0) {
            setClientErrors(["لطفا هتل را انتخاب کنید"]);
            window.scrollTo(0, 0);
            flag = false;
        }
        //check if from vehicles is empty
        if (fromVehicles.length === 0) {
            setClientErrors(["لطفا وسیله نقلیه را انتخاب کنید"]);
            window.scrollTo(0, 0);
            flag = false;
        }
        //check if to vehicles is empty
        if (toVehicles.length === 0) {
            setClientErrors(["لطفا وسیله نقلیه را انتخاب کنید"]);
            window.scrollTo(0, 0);
            flag = false;
        }

        if (flag) {
            //set confirm dialog

            let pastPrice = props.tour.payablePrice.replace(/,/g, "");
            let newPrice = totalPrice.replace(/,/g, "");
            let diff = newPrice - pastPrice;
            console.log("pastPrice", pastPrice);
            console.log("newPrice", newPrice);
            let text = `قیمت جدید تور شما ${totalPrice} میباشد و تفاوت قیمت ${diff} میباشد`;
            let displayDiff = //seperate three by three
                diff.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            if (diff > 0) {
                text = `قیمت جدید تور شما ${totalPrice} ریال می باشد و تفاوت قیمت ${displayDiff} ریال می باشد و به دلیل افزایش قیمت ما به التفاوت مبلغ باید توسط کاربر پرداخت و مجددا بررسی گردد`;
            } else {
                text = `قیمت جدید تور شما ${totalPrice} ریال می باشد و تفاوت قیمت ${displayDiff} ریال می باشد و به دلیل کاهش قیمت ما به التفاوت قیمت به کیف پول کاربر باز خواهد گشت و فاکتور تایید نهایی برای کاربر ارسال خواهد شد`;
            }

            setConfirmText(text);
            // setConfirmText("آیا از انجام این عملیات مطمئن هستید؟");
            setOpenConfirmDialog(true);
            setData("prices", [pastPrice, newPrice]);
            //old price
        }
    };

    const handleSubmit = async (e) => {
        put(route("userTour.update", props.tour.id)),
            {
                forceFormData: true,
            };
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    ویرایش تور
                </h2>
            }
        >
            <Head title="Dashboard" />
            <ConfirmDeleteDialog
                open={openConfirmDialog}
                setOpen={setOpenConfirmDialog}
                title="مطمئن هستید؟"
                text={confirmText}
                handleDoAction={handleSubmit}
                actionText="تایید"
            />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="flex flex-col">
                            <div className="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div className="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                                    <div className="overflow-hidden p-2 flex flex-col justify-center items-center">
                                        <ValidationErrors errors={errors} />
                                        {props.flash?.success && (
                                            <div className="mb-4 font-medium text-sm text-green-600">
                                                {props.flash?.success}
                                            </div>
                                        )}
                                        {clientErrors.length > 0 && (
                                            <div className="mb-4 font-medium text-sm text-red-600">
                                                {clientErrors.map(
                                                    (item, index) => (
                                                        <p key={index}>
                                                            {item}
                                                        </p>
                                                    )
                                                )}
                                            </div>
                                        )}

                                        <form
                                            className="w-full  max-w-xl"
                                            // onSubmit={submit}
                                            encType="multipart/form-data"
                                        >
                                            <h3>مشخصات تور: </h3>

                                            <div className="w-full flex justify-between">
                                                <div>
                                                    <div className="border p-5 m-5">
                                                        <p>
                                                            نوع رزرو کننده:{" "}
                                                            {props.tour.agency
                                                                ? "آژانس"
                                                                : "خریدار نهایی"}
                                                        </p>
                                                        <p>
                                                            نام رزرو کننده:{" "}
                                                            {props.tour.agency
                                                                ? props.tour
                                                                      .agency
                                                                      .agency_name
                                                                : props.tour
                                                                      .user
                                                                      .name +
                                                                  " " +
                                                                  props.tour
                                                                      .user
                                                                      .last_name}
                                                        </p>
                                                    </div>
                                                    <div className="border p-5 m-5">
                                                        <p>
                                                            مبدا:{" "}
                                                            {
                                                                props.tour
                                                                    .from_city
                                                                    .title
                                                            }
                                                        </p>
                                                        <p>
                                                            مقصد:{" "}
                                                            {
                                                                props.tour
                                                                    .to_city
                                                                    .title
                                                            }
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div className="border p-5 m-5">
                                                        <p>
                                                            تعداد بزرگسال:{" "}
                                                            {
                                                                props.tour
                                                                    .adult_count
                                                            }
                                                        </p>
                                                        <p>
                                                            تعداد نوجوان:{" "}
                                                            {
                                                                props.tour
                                                                    .teen_count
                                                            }
                                                        </p>
                                                        <p>
                                                            تعداد کودک:{" "}
                                                            {
                                                                props.tour
                                                                    .kid_count
                                                            }
                                                        </p>
                                                        <p>
                                                            تعداد نوزاد:{" "}
                                                            {
                                                                props.tour
                                                                    .infant_count
                                                            }
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <h3>ویرایش اطلاعات تور: </h3>
                                            <div className="flex flex-wrap mb-6 border m-5 p-5">
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="departure_date_time"
                                                        value="تاریخ رفت "
                                                    />
                                                    <DatePicker
                                                        timePicker={false}
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.departure_date_time
                                                        }
                                                        onClickSubmitButton={
                                                            onHandleChangeTimeFrom
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_date_time"
                                                        value="تاریخ برگشت "
                                                    />
                                                    <DatePicker
                                                        timePicker={false}
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.arrival_date_time
                                                        }
                                                        onClickSubmitButton={
                                                            onHandleChangeTimeTo
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="hotel"
                                                        value="هتل"
                                                        className="mb-2"
                                                    />
                                                    <Select
                                                        name="hotel"
                                                        value={data.hotel}
                                                        className="mt-1 block w-full"
                                                        autoComplete="hotel"
                                                        isFocused={true}
                                                        values={hotels.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.name,
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="departure_transport_type"
                                                        value="نوع وسیله رفت"
                                                        className="mb-2"
                                                    />
                                                    <Select
                                                        name="departure_transport_type"
                                                        value={
                                                            data.departure_transport_type
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="departure_transport_type"
                                                        isFocused={true}
                                                        values={[
                                                            {
                                                                title: "هواپیما",
                                                                value: "AIRPLANE",
                                                            },
                                                            {
                                                                title: "قطار",
                                                                value: "TRAIN",
                                                            },
                                                            {
                                                                title: "اتوبوس",
                                                                value: "BUS",
                                                            },
                                                        ]}
                                                        handleChange={(e) => {
                                                            onHandleChangeDepartureVehicle(
                                                                e
                                                            );
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="departure_transport_vehicle"
                                                        value="نام وسیله رفت"
                                                    />
                                                    <Select
                                                        name="departure_transport_vehicle"
                                                        value={
                                                            data.departure_transport_vehicle
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="departure_transport_vehicle"
                                                        isFocused={true}
                                                        values={fromVehicles.map(
                                                            (item) => {
                                                                return {
                                                                    title: renderVehicleName(
                                                                        item
                                                                    ),
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_transport_type"
                                                        value="نوع وسیله برگشت"
                                                        className="mb-2"
                                                    />
                                                    <Select
                                                        name="arrival_transport_type"
                                                        value={
                                                            data.arrival_transport_type
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="arrival_transport_type"
                                                        isFocused={true}
                                                        values={[
                                                            {
                                                                title: "هواپیما",
                                                                value: "AIRPLANE",
                                                            },
                                                            {
                                                                title: "قطار",
                                                                value: "TRAIN",
                                                            },
                                                            {
                                                                title: "اتوبوس",
                                                                value: "BUS",
                                                            },
                                                        ]}
                                                        handleChange={(e) => {
                                                            onHandleChangeArrivalVehicle(
                                                                e
                                                            );
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_transport_vehicle"
                                                        value="نام وسیله برگشت"
                                                    />
                                                    <Select
                                                        name="arrival_transport_vehicle"
                                                        value={
                                                            data.arrival_transport_vehicle
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="arrival_transport_vehicle"
                                                        isFocused={true}
                                                        values={toVehicles.map(
                                                            (item) => {
                                                                return {
                                                                    title: renderVehicleName(
                                                                        item
                                                                    ),
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="flex w-full px-3 mb-6">
                                                    <label className="flex items-center ">
                                                        <Radio
                                                            name="fullboard"
                                                            value={1}
                                                            checked={
                                                                data.fullboard ==
                                                                1
                                                            }
                                                            handleChange={
                                                                onHandleChange
                                                            }
                                                        />
                                                        <span className="mr-2 ml-2 text-sm text-gray-600">
                                                            فول برد
                                                        </span>
                                                    </label>
                                                    <label className="flex items-center">
                                                        <Radio
                                                            name="fullboard"
                                                            value={0}
                                                            checked={
                                                                data.fullboard ==
                                                                0
                                                            }
                                                            handleChange={
                                                                onHandleChange
                                                            }
                                                        />

                                                        <span className="mr-2 ml-2 text-sm text-gray-600">
                                                            با صبحانه
                                                        </span>
                                                    </label>
                                                </div>

                                                <div className=" flex flex-wrap w-full px-3 mb-6">
                                                    {props.services.map(
                                                        (item) => (
                                                            <div
                                                                key={item.id}
                                                                className="block mt-4"
                                                            >
                                                                <label className="flex items-center">
                                                                    <Checkbox
                                                                        name="services"
                                                                        checked={
                                                                            data.services.indexOf(
                                                                                item.id.toString()
                                                                            ) !=
                                                                            -1
                                                                        }
                                                                        value={
                                                                            item.id
                                                                        }
                                                                        handleChange={
                                                                            onHandleChange
                                                                        }
                                                                    />
                                                                    <span className="ml-2 mr-2 text-sm text-gray-600">
                                                                        {
                                                                            item.name
                                                                        }
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        )
                                                    )}
                                                </div>
                                            </div>
                                            <h3> اطلاعات اتاق ها: </h3>
                                            <div className="flex flex-wrap mb-6 border m-5 p-5">
                                                {data.rooms.map(
                                                    (item, index) => {
                                                        return (
                                                            <>
                                                                {/* delete room */}
                                                                <div className="w-full px-3 mb-6">
                                                                    <div
                                                                        onClick={() =>
                                                                            removeRoom(
                                                                                item.id
                                                                            )
                                                                        }
                                                                        className="cursor-pointer bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                                                    >
                                                                        حذف اتاق{" "}
                                                                        {index +
                                                                            1}
                                                                    </div>
                                                                </div>

                                                                <TourRoom
                                                                    key={index}
                                                                    index={
                                                                        index
                                                                    }
                                                                    name={
                                                                        item.name
                                                                    }
                                                                    setName={(
                                                                        data
                                                                    ) =>
                                                                        changeRoomName(
                                                                            item.id,
                                                                            data
                                                                        )
                                                                    }
                                                                    capacity={
                                                                        item.capacity
                                                                    }
                                                                    setCapacity={(
                                                                        data
                                                                    ) =>
                                                                        changeRoomCapacity(
                                                                            item.id,
                                                                            data
                                                                        )
                                                                    }
                                                                />
                                                            </>
                                                        );
                                                    }
                                                )}
                                            </div>
                                            {/* add room btn */}
                                            <div className="w-full px-3 mb-6">
                                                <div
                                                    onClick={() => addRoom()}
                                                    className="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                >
                                                    افزودن اتاق
                                                </div>
                                            </div>
                                            <h3> اطلاعات مالی: </h3>
                                            <div className="flex flex-col flex-wrap mb-6 border m-5 p-5">
                                                <p>
                                                    هزینه نهایی قابل پرداخت:{" "}
                                                    {totalPrice} ریال
                                                </p>
                                                <p>
                                                    هزینه پرداخت شده:
                                                    {
                                                        props.tour?.paied_price
                                                    }{" "}
                                                    ریال
                                                </p>
                                            </div>

                                            {/* //submit button */}
                                            <div className="flex flex-wrap w-full px-3 mb-6">
                                                <div
                                                    onClick={handleProcess}
                                                    className="cursor-pointer w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                                >
                                                    ثبت تغییرات
                                                </div>
                                            </div>
                                        </form>
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
