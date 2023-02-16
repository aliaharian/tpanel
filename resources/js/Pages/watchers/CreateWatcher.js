import React, { useEffect } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import ValidationErrors from "@/Components/ValidationErrors";
import Radio from "@/Components/Radio";
import Select from "@/Components/Select";
import { DatePicker } from "react-advance-jalaali-datepicker";
import Checkbox from "@/Components/Checkbox";
import moment from "jalali-moment";

export default function CreateWatcher(props) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        agency_name: props.watcher?.agency_id,
        isHaghighi:
            props.watcher?.is_haghighi == 1 ? "true" : "false" || "true",
        buyer_name: props.watcher?.buyer_name || "",
        national_code: props.watcher?.buyer_national_code || "",
        fullboard: props.watcher?.fullboard == 1 ? "true" : "false" || "false",
        mobile: props.watcher?.mobile_phone || "",
        people_count: props.watcher?.people_count || 2,
        departure_transport_type:
            props.watcher?.departure_transport_type || "AIRPLANE",
        departure_transport_vehicle:
            props.watcher?.departure_vehicle_id || "",
        // departure_transport_name: props.watcher?.departure_transport_name || "",
        // departure_transport_logo: null,
        // departure_date: props.watcher?.departure_date || "",
        // departure_time: props.watcher?.departure_time || "",
        arrival_transport_type:
            props.watcher?.arrival_transport_type || "AIRPLANE",
        arrival_transport_vehicle:
            props.watcher?.arrival_vehicle_id || "",
        // arrival_transport_name: props.watcher?.arrival_transport_name || "",
        // arrival_transport_logo: null,
        // arrival_date: props.watcher?.arrival_date || "",
        // arrival_time: props.watcher?.arrival_time || "",
        hotel_name: props.watcher?.hotel_name || "",
        rooms_count: props.watcher?.room_numbers || 1,
        room_type: props.watcher?.room_type || "",
        stay_length: props.watcher?.stay_length || 2,
        services:
            props.watcher?.services.map((item) => item.id.toString()) || [],
        pricePerPerson: props.watcher?.price_per_adult || 0,
        payablePrice: props.watcher?.total_price || 0,
        fromProvince: props.watcher?.from_city.parent || "8",
        fromCity: props.watcher?.from_city.id || "360",
        toProvince: props.watcher?.to_city.parent || "11",
        toCity: props.watcher?.to_city.id || "522",
    });
    const [fromCities, setFromCities] = React.useState([]);
    const [toCities, setToCities] = React.useState([]);

    const [fromVehicles, setFromVehicles] = React.useState([]);
    const [toVehicles, setToVehicles] = React.useState([]);

    const onHandleChangeDepartureDate = (val, l) => {
        // console.log(val, l);

        setData("departure_date", val);
    };
    const onHandleChangeArrivalDate = (val, l) => {
        setData("arrival_date", val);
    };
    const onHandleChange = (event) => {
        // console.log(typeof event);

        if (event.target.type === "checkbox") {
            let tmp = data[event.target.name];
            // console.log(
            //     "tmp.indexOf[event.target.value]",
            //     tmp.indexOf(event.target.value)
            // );
            // console.log("tmp.indexOf[event.target.value]", event.target.value);
            if (tmp.indexOf(event.target.value) === -1) {
                // console.log("tmp", event.target.value);

                tmp = [...tmp, event.target.value];
            } else {
                tmp.splice(tmp.indexOf(event.target.value), 1);
            }
            // console.log("tmp", tmp);

            setData(event.target.name, [...tmp]);
        } else if (event.target.type === "file") {
            // console.log(event.target.files[0]);
            setData(event.target.name, event.target.files[0]);
        } else {
            setData(event.target.name, event.target.value);
        }
    };
    // console.log(props.watcher);

    React.useEffect(() => {
        if (props.watcher) {
            loadFromCities({
                target: { value: props.watcher?.from_city.parent },
            });
            loadToCities({ target: { value: props.watcher?.to_city.parent } });
        } else {
            loadFromCities({
                target: { value: 8 },
            });
            loadToCities({ target: { value: 11 } });
        }
    }, []);
    const submit = (e) => {
        e.preventDefault();
        if (props.watcher) {
            put(route("watchers.update", props.watcher.id)),
                {
                    forceFormData: true,
                };
        } else {
            post(route("watchers.store"), { forceFormData: true });
        }
        setData("name", "");
    };
    const DatePickerInput = (props) => {
        return (
            <input
                className="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                {...props}
            />
        );
    };
    const loadFromCities = async (e) => {
        const response = await axios.post(route("loadCity"), {
            province: e.target.value,
        });

        setFromCities(response.data);
    };
    const checkLoadVehicle = () => {
        console.log("inside in", data);
        if (data.fromCity && data.toCity && data.departure_transport_type) {
            loadFromTransportVehicles();
            console.log("hereeee");
        }
        if (data.toCity && data.fromCity && data.arrival_transport_type) {
            loadToTransportVehicles();
            console.log("hereeee2");
        }
    };
    useEffect(() => {
        checkLoadVehicle();
    }, [
        data.fromCity,
        data.toCity,
        data.departure_transport_type,
        data.arrival_transport_type,
    ]);
    const loadFromTransportVehicles = async () => {
        if (data.fromCity && data.toCity && data.departure_transport_type) {
            const response = await axios.post(route("loadTransportVehicles"), {
                type: data.departure_transport_type,
                from: data.fromCity,
                to: data.toCity,
            });
            setFromVehicles(response.data);
        } else {
            setFromVehicles([]);
        }
    };
    const loadToTransportVehicles = async () => {
        if (data.fromCity && data.toCity && data.arrival_transport_type) {
            const response = await axios.post(route("loadTransportVehicles"), {
                type: data.arrival_transport_type,
                from: data.toCity,
                to: data.fromCity,
            });
            setToVehicles(response.data);
        } else {
            setToVehicles([]);
        }
    };
    const loadToCities = async (e) => {
        const response = await axios.post(route("loadCity"), {
            province: e.target.value,
        });

        setToCities(response.data);
    };
    const renderVehicleName = (item) => {
        moment.locale("fa", { useGregorianParser: true });

        let date = moment(parseInt(item.departure_date_time)).format(
            "jYYYY/jMM/jDD HH:mm"
        );
        console.log("date", date);

        return `${item.name} از شرکت ${item.transport_company.name} حرکت تاریخ ${date}
        `;
    };

    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {props.service ? "ویرایش" : "تعریف"} واچر{" "}
                </h2>
            }
        >
            <Head title="Dashboard" />

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

                                        <form
                                            className="w-full max-w-lg"
                                            onSubmit={submit}
                                            encType="multipart/form-data"
                                        >
                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="flex w-full px-3 mb-6">
                                                    <label className="flex items-center ">
                                                        <Radio
                                                            name="isHaghighi"
                                                            value={"true"}
                                                            checked={
                                                                data.isHaghighi ==
                                                                "true"
                                                            }
                                                            handleChange={
                                                                onHandleChange
                                                            }
                                                        />
                                                        <span className="ml-2 mr-2 ml-2 text-sm text-gray-600">
                                                            کاربر حقیقی
                                                        </span>
                                                    </label>
                                                    <label className="flex items-center">
                                                        <Radio
                                                            name="isHaghighi"
                                                            value={"false"}
                                                            checked={
                                                                data.isHaghighi ==
                                                                "false"
                                                            }
                                                            handleChange={
                                                                onHandleChange
                                                            }
                                                        />

                                                        <span className="ml-2 mr-2 ml-2 text-sm text-gray-600">
                                                            کاربر حقوقی
                                                        </span>
                                                    </label>
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="fromProvince"
                                                        value="استان مبدا"
                                                    />
                                                    <Select
                                                        name="fromProvince"
                                                        value={
                                                            data.fromProvince
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="fromProvince"
                                                        isFocused={true}
                                                        values={props.provinces.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.title,
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                            loadFromCities(e);
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="fromCity"
                                                        value="شهر مبدا"
                                                    />
                                                    <Select
                                                        name="fromCity"
                                                        value={data.fromCity}
                                                        className="mt-1 block w-full"
                                                        autoComplete="fromCity"
                                                        isFocused={true}
                                                        values={fromCities?.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.title,
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="toProvince"
                                                        value="استان مقصد"
                                                    />
                                                    <Select
                                                        name="toProvince"
                                                        value={data.toProvince}
                                                        className="mt-1 block w-full"
                                                        autoComplete="toProvince"
                                                        isFocused={true}
                                                        values={props.provinces.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.title,
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                            loadToCities(e);
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="toCity"
                                                        value="شهر مقصد"
                                                    />
                                                    <Select
                                                        name="toCity"
                                                        value={data.toCity}
                                                        className="mt-1 block w-full"
                                                        autoComplete="toCity"
                                                        isFocused={true}
                                                        values={toCities?.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.title,
                                                                    value: item.id,
                                                                    key: item.id,
                                                                };
                                                            }
                                                        )}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                            // checkLoadVehicle();
                                                        }}
                                                    />
                                                </div>
                                                {data.isHaghighi == "false" && (
                                                    <>
                                                        <div className="w-full px-3 mb-6">
                                                            <Label
                                                                forInput="agency_name"
                                                                value="نام آژانس"
                                                            />
                                                            <Select
                                                                name="agency_name"
                                                                value={
                                                                    data.agency_name
                                                                }
                                                                className="mt-1 block w-full"
                                                                autoComplete="agency_name"
                                                                isFocused={true}
                                                                values={props.agencies.map(
                                                                    (item) => {
                                                                        return {
                                                                            title: item.agency_name,
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
                                                    </>
                                                )}
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="buyer_name"
                                                        value="نام و نام خانوادگی سرپرست مسافران"
                                                    />
                                                    <Input
                                                        type="text"
                                                        name="buyer_name"
                                                        value={data.buyer_name}
                                                        className="mt-1 block w-full"
                                                        autoComplete="buyer_name"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="national_code"
                                                        value="کد ملی سرپرست مسافران"
                                                    />
                                                    <Input
                                                        type="number"
                                                        name="national_code"
                                                        value={
                                                            data.national_code
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="national_code"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="mobile"
                                                        value="تلفن همراه سرپرست مسافران"
                                                    />
                                                    <Input
                                                        type="number"
                                                        name="mobile"
                                                        value={data.mobile}
                                                        className="mt-1 block w-full"
                                                        autoComplete="mobile"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="people_count"
                                                        value="تعداد مسافران"
                                                    />
                                                    <Input
                                                        type="number"
                                                        name="people_count"
                                                        value={
                                                            data.people_count
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="people_count"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="departure_transport_type"
                                                        value="نوع وسیله رفت"
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
                                                            onHandleChange(e);
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

                                                {/* <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="departure_transport_name"
                                                        value="نام شرکت حمل و نقل رفت"
                                                    />
                                                    <Input
                                                        type="text"
                                                        name="departure_transport_name"
                                                        value={
                                                            data.departure_transport_name
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="departure_transport_name"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 mb-6">
                                                    <label
                                                        className="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                                        htmlFor="departure_transport_logo"
                                                    >
                                                        لوگو شرکت حمل و نقل رفت
                                                    </label>
                                                    {!props.watcher && (
                                                        <input
                                                            className="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                                            type="file"
                                                            name="departure_transport_logo"
                                                            id="departure_transport_logo"
                                                            onChange={
                                                                onHandleChange
                                                            }
                                                        />
                                                    )}
                                                    {props.watcher
                                                        ?.departure_transport_logo && (
                                                        <img
                                                            src={
                                                                "/uploads/" +
                                                                props.watcher
                                                                    ?.departure_transport_logo
                                                            }
                                                        />
                                                    )}
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="departure_date"
                                                        value="تاریخ رفت"
                                                    />
                                                    <DatePicker
                                                        inputComponent={
                                                            DatePickerInput
                                                        }
                                                        placeholder="انتخاب تاریخ"
                                                        format="jYYYY/jMM/jDD"
                                                        onChange={
                                                            onHandleChangeDepartureDate
                                                        }
                                                        id="departure_date"
                                                        name="departure_date"
                                                        value={
                                                            data.departure_date
                                                        }
                                                        preSelected={
                                                            new Date(
                                                                data.departure_date *
                                                                    1000
                                                            ).toLocaleDateString(
                                                                "fa-IR"
                                                            ) || false
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="departure_time"
                                                        value="ساعت رفت"
                                                    />
                                                    <Input
                                                        type="time"
                                                        name="departure_time"
                                                        value={
                                                            data.departure_time
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="departure_time"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div> */}

                                                {/* //arrival */}

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_transport_type"
                                                        value="نوع وسیله برگشت"
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
                                                            onHandleChange(e);
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

                                                {/* <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_transport_name"
                                                        value="نام شرکت حمل و نقل برگشت"
                                                    />
                                                    <Input
                                                        type="text"
                                                        name="arrival_transport_name"
                                                        value={
                                                            data.arrival_transport_name
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="arrival_transport_name"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 mb-6">
                                                    <label
                                                        className="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                                        htmlFor="arrival_transport_logo"
                                                    >
                                                        لوگو شرکت حمل و نقل
                                                        برگشت
                                                    </label>
                                                    {!props.watcher && (
                                                        <input
                                                            className="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                                            type="file"
                                                            name="arrival_transport_logo"
                                                            id="arrival_transport_logo"
                                                            onChange={
                                                                onHandleChange
                                                            }
                                                        />
                                                    )}
                                                    {props.watcher
                                                        ?.arrival_transport_logo && (
                                                        <img
                                                            src={
                                                                "/uploads/" +
                                                                props.watcher
                                                                    ?.arrival_transport_logo
                                                            }
                                                        />
                                                    )}
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_date"
                                                        value="تاریخ برگشت"
                                                    />
                                                    <DatePicker
                                                        inputComponent={
                                                            DatePickerInput
                                                        }
                                                        placeholder="انتخاب تاریخ"
                                                        format="jYYYY/jMM/jDD"
                                                        onChange={
                                                            onHandleChangeArrivalDate
                                                        }
                                                        id="arrival_date"
                                                        name="arrival_date"
                                                        value={
                                                            data.arrival_date
                                                        }
                                                        preSelected={
                                                            new Date(
                                                                data.arrival_date *
                                                                    1000
                                                            ).toLocaleDateString(
                                                                "fa-IR"
                                                            ) || false
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_time"
                                                        value="ساعت برگشت"
                                                    />
                                                    <Input
                                                        type="time"
                                                        name="arrival_time"
                                                        value={
                                                            data.arrival_time
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="arrival_time"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div> */}

                                                {/* //hote */}
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="hotel_name"
                                                        value="نام هتل"
                                                    />
                                                    <Input
                                                        type="text"
                                                        name="hotel_name"
                                                        value={data.hotel_name}
                                                        className="mt-1 block w-full"
                                                        autoComplete="hotel_name"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="rooms_count"
                                                        value="تعداد اتاق"
                                                    />
                                                    <Input
                                                        type="number"
                                                        name="rooms_count"
                                                        value={data.rooms_count}
                                                        className="mt-1 block w-full"
                                                        autoComplete="rooms_count"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="room_type"
                                                        value="نوع اتاق"
                                                    />
                                                    <Input
                                                        type="text"
                                                        name="room_type"
                                                        value={data.room_type}
                                                        className="mt-1 block w-full"
                                                        autoComplete="room_type"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="flex w-full px-3 mb-6">
                                                    <label className="flex items-center ">
                                                        <Radio
                                                            name="fullboard"
                                                            value={"true"}
                                                            checked={
                                                                data.fullboard ==
                                                                "true"
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
                                                            value={"false"}
                                                            checked={
                                                                data.fullboard ==
                                                                "false"
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
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="stay_length"
                                                        value="تعداد شب اقامت در هتل"
                                                    />
                                                    <Input
                                                        type="number"
                                                        name="stay_length"
                                                        value={data.stay_length}
                                                        className="mt-1 block w-full"
                                                        autoComplete="stay_length"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
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

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="pricePerPerson"
                                                        value="قیمت به ازای هر بزرگسال"
                                                    />
                                                    <Input
                                                        type="currency"
                                                        name="pricePerPerson"
                                                        value={
                                                            data.pricePerPerson
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="pricePerPerson"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="payablePrice"
                                                        value="مبلغ قابل پرداخت"
                                                    />
                                                    <Input
                                                        type="currency"
                                                        name="payablePrice"
                                                        value={
                                                            data.payablePrice
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="payablePrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>

                                            <button className="bg-blue-500 hover:bg-blue-700 w-full text-white font-bold py-2 px-4 rounded-full">
                                                {props.service
                                                    ? "ویرایش"
                                                    : "ایجاد"}
                                            </button>
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
