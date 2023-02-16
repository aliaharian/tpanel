import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import { useEffect } from "react/cjs/react.production.min";
import ValidationErrors from "@/Components/ValidationErrors";
import Select from "@/Components/Select";
import { Inertia } from "@inertiajs/inertia";
import { DatePicker } from "jalali-react-datepicker";

export default function CreateTransportVehicle(props) {
    const [uploadDone, setUploadDone] = React.useState(true);
    const [fromCities, setFromCities] = React.useState([]);
    const [transportCompanies, setTransportCompanies] = React.useState([]);
    const [toCities, setToCities] = React.useState([]);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: props.data?.name,
        fromProvince: props.data?.from_city.parent,
        fromCity: props.data?.from_city.id,
        toProvince: props.data?.to_city.parent,
        toCity: props.data?.to_city.id,
        type: props.data?.transport_type,
        transportCompany: props.data?.transport_company.id,
        departure_date: props.data?.departure_date_time
            ? parseInt(props.data?.departure_date_time)
            : undefined,
        arrival_date: props.data?.arrival_date_time
            ? parseInt(props.data?.arrival_date_time)
            : undefined,
        transportNumber: props.data?.transport_number,
        transportClass: props.data?.transport_class,
        capacity: props.data?.capacity,
        adultPrice: props.data?.adult_price,
        teenPrice: props.data?.teen_price,
        kidPrice: props.data?.kid_price,
        infantPrice: props.data?.infant_price,
    });
    React.useEffect(() => {
        if (props.data) {
            loadFromCities({ target: { value: props.data?.from_city.parent } });
            loadToCities({ target: { value: props.data?.to_city.parent } });
            loadTransportCompanies({
                target: { value: props.data?.transport_type },
            });
        }
    }, []);

    const onHandleChange = (event) => {
        if (event.target.type === "file") {
            // setUploadDone(false)
            console.log("pipoipoii", event.target.files[0]);
            setData(event.target.name, event.target.files[0]);
        } else {
            if (type === "currency") {
                setData(
                    event.target.name,
                    event.target.value.replaceAll(",", "")
                );
            } else {
                setData(
                    event.target.name,
                    event.target.type === "checkbox"
                        ? event.target.checked
                        : event.target.value
                );
            }
        }
    };


    const loadFromCities = async (e) => {
        const response = await axios.post(route("loadCity"), {
            province: e.target.value,
        });
        setFromCities(response.data);
    };
    const loadToCities = async (e) => {
        const response = await axios.post(route("loadCity"), {
            province: e.target.value,
        });

        setToCities(response.data);
    };
    const loadTransportCompanies = async (e) => {
        const response = await axios.post(route("loadTransportCompanies"), {
            type: e.target.value,
        });
        setTransportCompanies(response.data);
    };

    const submit = (e) => {
        e.preventDefault();

        if (props.data) {
            put(route("transportVehicles.update", props?.data?.id));
        } else {
            post(route("transportVehicles.store"));
        }
    };
    const DatePickerInput = (props) => {
        return (
            <input
                className="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                {...props}
            />
        );
    };
    const onHandleChangeDepartureDate = ({ value }) => {
        let val = Math.floor(value);
        setData("departure_date", val);
    };
    const onHandleChangeArrivalDate = ({ value }) => {
        let val = Math.floor(value);
        setData("arrival_date", val);
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {props.co ? "ویرایش" : "ایجاد"} وسیله حمل و نقل{" "}
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="flex flex-col">
                            <div className="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div className="py-4 inline-block min-w-full sm:px-6 lg:px-8  flex flex-col justify-center items-center">
                                    <ValidationErrors errors={errors} />

                                    <div className="overflow-hidden p-2 flex justify-center items-center">
                                        {/* <ValidationErrors errors={errors} /> */}

                                        <form
                                            className="w-full max-w-lg"
                                            onSubmit={submit}
                                            encType="multipart/form-data"
                                        >
                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="name"
                                                        value="نام"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="name"
                                                        value={data.name}
                                                        className="mt-1 block w-full"
                                                        autoComplete="name"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
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
                                                        handleChange={
                                                            onHandleChange
                                                        }
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
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-1/2 mb-6 px-3">
                                                    <Label
                                                        forInput="type"
                                                        value="نوع حمل و نقل"
                                                    />
                                                    <Select
                                                        name="type"
                                                        value={data.type}
                                                        className="mt-1 block w-full"
                                                        autoComplete="type"
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
                                                            loadTransportCompanies(
                                                                e
                                                            );
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="transportCompany"
                                                        value="شرکت حمل و نقل"
                                                    />
                                                    <Select
                                                        name="transportCompany"
                                                        value={
                                                            data.transportCompany
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="transportCompany"
                                                        isFocused={true}
                                                        values={transportCompanies?.map(
                                                            (item) => {
                                                                return {
                                                                    title: item.name,
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

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="departure_date"
                                                        value="تاریخ و ساعت حرکت"
                                                    />
                                                    <DatePicker
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.departure_date
                                                        }
                                                        onClickSubmitButton={
                                                            onHandleChangeDepartureDate
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="arrival_date"
                                                        value="تاریخ و ساعت رسیدن"
                                                    />
                                                    <DatePicker
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.arrival_date
                                                        }
                                                        onClickSubmitButton={
                                                            onHandleChangeArrivalDate
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="transportNumber"
                                                        value="شماره پرواز / شماره قطار / شماره اتوبوس"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="transportNumber"
                                                        value={
                                                            data.transportNumber
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="transportNumber"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="transportClass"
                                                        value="کلاس پرواز / کلاس قطار / کلاس اتوبوس"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="transportClass"
                                                        value={
                                                            data.transportClass
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="transportClass"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="capacity"
                                                        value="ظرفیت"
                                                    />

                                                    <Input
                                                        type="number"
                                                        name="capacity"
                                                        value={data.capacity}
                                                        className="mt-1 block w-full"
                                                        autoComplete="capacity"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="adultPrice"
                                                        value="قیمت برای هر بزرگسال(بالای ۱۲ سال)"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="adultPrice"
                                                        value={data.adultPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="adultPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="teenPrice"
                                                        value="قیمت برای هر نوجوان(بین ۸ تا ۱۲ سال)"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="teenPrice"
                                                        value={data.teenPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="teenPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="kidPrice"
                                                        value="قیمت برای هر کودک(بین ۳ تا ۸ سال)"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="kidPrice"
                                                        value={data.kidPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="kidPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="infantPrice"
                                                        value="قیمت برای هر نوزاد(تا ۳ سال)"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="infantPrice"
                                                        value={data.infantPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="infantPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            <button className="bg-blue-500 hover:bg-blue-700 w-full text-white font-bold py-2 px-4 rounded-full">
                                                {props.co ? "ویرایش" : "ایجاد"}
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
