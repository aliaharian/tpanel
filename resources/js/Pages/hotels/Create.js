import React, { useEffect, useRef, useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import ValidationErrors from "@/Components/ValidationErrors";
import Select from "@/Components/Select";
import { Inertia } from "@inertiajs/inertia";
// import { DatePicker } from "jalali-react-datepicker";
// import { setRTLTextPlugin } from "!mapbox-gl";
import mapboxgl from "!mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import RichText from "@/Components/RichText";
import Checkbox from "@/Components/Checkbox";
import { MobileDatePicker } from "@mui/x-date-pickers";

mapboxgl.accessToken =
    "pk.eyJ1IjoiYWxpYWhhcmlhbjUiLCJhIjoiY2xjb2ltbHh3MWd1dTNvcnlseDQwYjM3MyJ9.004s4ZJVDeXZgb_VLqePrA";
export default function CreateHotel(props) {
    const [uploadDone, setUploadDone] = React.useState(true);
    const [cities, setCities] = React.useState([]);
    const mapContainer = useRef(null);
    const map = useRef(null);
    const marker = useRef(null);
    // console.log("dataaaa", props);

    const [lng, setLng] = useState(props.data?.longitude || 59.61577063697962);
    const [lat, setLat] = useState(props.data?.latitude || 36.287943550320406);
    const [zoom, setZoom] = useState(12);
    //lat and long state
    const [latlng, setLatlng] = useState({
        lng: props.data?.longitude || null,
        lat: props.data?.latitude || null,
    });
    const bounds = [
        [44.410799, 24.231581], // Southwest coordinates
        [61.62452, 41.805653], // Northeast coordinates
    ];
    const { data, setData, post, put, processing, errors, reset } = useForm({
        //hotel items from Hotel model
        name: props.data?.name || "",
        stars: props.data?.stars || "",
        type: props.data?.type || "",
        address: props.data?.address || "",
        rate: props.data?.rate || "",
        city_id: props.data?.city_id || "522",
        province_id: props.data?.province_id || "11",
        latitude: props.data?.latitude || "",
        // longitude: props.data?.longitude || "",
        description: props.data?.description || "",
        check_in: props.data?.check_in || "",
        check_out: props.data?.check_out || "",
        image: null,
        currImage: props.data?.image || "",
        notes: props.data?.notes || "",
        capacity: props.data?.capacity || "",
        available_time_from: props.data?.available_time_from
            ? parseInt(props.data?.available_time_from)
            : undefined,
        available_time_to: props.data?.available_time_to
            ? parseInt(props.data?.available_time_to)
            : undefined,
        adultPrice: props.data?.adult_price.toString() || "",
        teenPrice: props.data?.teen_price.toString() || "",
        kidPrice: props.data?.kid_price.toString() || "",
        infantPrice: props.data?.infant_price.toString() || "",
        fullboardPrice: props.data?.fullboard_price.toString() || "",
        //early check in price
        earlyCheckInPrice: props.data?.early_check_in_price || "",
        //late check out price
        lateCheckOutPrice: props.data?.late_check_out_price || "",
        //breakfast price
        breakfastPrice: props.data?.free_breakfast_price || "",
        //lunch price
        lunchPrice: props.data?.free_lunch_price || "",
        //dinner price
        dinnerPrice: props.data?.free_dinner_price || "",
        hotelServices:
            props.data?.hotel_services?.map((item) => item.id.toString()) || [],
        roomServices:
            props.data?.room_services?.map((item) => item.id.toString()) || [],
    });

    //set mapbox rtl plugin and permissions
    // useEffect(() => {
    //     setRTLTextPlugin(
    //         "https://www.parsimap.com/scripts/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.2.0/mapbox-gl-rtl-text.js"
    //     );
    // }, []);

    React.useEffect(() => {
        if (props.data) {
            loadCities({ target: { value: props.data?.city_place.parent } });
            // loadTransportCompanies({
            //     target: { value: props.data?.transport_type },
            // });
        } else {
            loadCities({
                target: { value: 11 },
            });
        }
    }, []);

    //load and update mapbox map
    useEffect(() => {
        if (map.current) return; // initialize map only once
        map.current = new mapboxgl.Map({
            container: mapContainer.current,
            style: "/map/street.json", // style URL
            center: [lng, lat], // starting position
            zoom: zoom,
            maxBounds: bounds,
        });

        map.current.on("move", () => {
            setLng(map.current.getCenter().lng.toFixed(4));
            setLat(map.current.getCenter().lat.toFixed(4));
            setZoom(map.current.getZoom().toFixed(2));
        });
        map.current.on("load", () => {
            //   setMapLoaded(true);
            marker.current = new mapboxgl.Marker({
                color: "red",
                draggable: true,
                scale: 1,
            })
                .setLngLat([
                    props.data?.longitude || "59.61577063697962",
                    props.data?.latitude || "36.287943550320406",
                ])
                .addTo(map.current);

            marker.current.on("dragend", () => {
                //   setMapLoaded(true);
                // setData("latitude", marker.current.getLngLat().lat);
                // setData("longitude", marker.current.getLngLat().lng);
                console.log(marker.current.getLngLat().lat);
                console.log(marker.current.getLngLat().lng);
                setLatlng(marker.current.getLngLat());
            });
        });
    });
    // console.log("latlng", latlng);
    //fill data useEffect latlng
    useEffect(() => {
        setData("latitude", latlng);
    }, [latlng]);

    // console.log("data", data);

    const onHandleChange = (event) => {
        if (event.target.type === "checkbox") {
            let tmp = data[event.target.name];
            if (tmp.indexOf(event.target.value) === -1) {
                tmp = [...tmp, event.target.value];
            } else {
                tmp.splice(tmp.indexOf(event.target.value), 1);
            }

            setData(event.target.name, [...tmp]);
        } else if (event.target.type === "file") {
            // setUploadDone(false)
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

    const loadCities = async (e) => {
        const response = await axios.post(route("loadCity"), {
            province: e.target.value,
        });
        setCities(response.data);
    };

    const submit = (e) => {
        e.preventDefault();

        if (props.data) {
            Inertia.post(route("hotels.update", props.data.id), {
                _method: "put",
                ...data,
            });
        } else {
            post(route("hotels.store"));
        }
    };

    const onHandleChangeTimeFrom = (value) => {
        let val = Math.floor(new Date(value).getTime());
        setData("available_time_from", val);
    };
    const onHandleChangeTimeTo = (value) => {
        let val = Math.floor(new Date(value).getTime());
        setData("available_time_to", val);
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {props.co ? "ویرایش" : "ایجاد"} هتل{" "}
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
                                                        forInput="province_id"
                                                        value="استان"
                                                    />
                                                    <Select
                                                        name="province_id"
                                                        value={data.province_id}
                                                        className="mt-1 block w-full"
                                                        autoComplete="province_id"
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
                                                            loadCities(e);
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="city_id"
                                                        value="شهر"
                                                    />
                                                    <Select
                                                        name="city_id"
                                                        value={data.city_id}
                                                        className="mt-1 block w-full"
                                                        autoComplete="city_id"
                                                        isFocused={true}
                                                        values={cities?.map(
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
                                                <div className="w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="stars"
                                                        value="ستاره هتل"
                                                    />

                                                    <Input
                                                        type="number"
                                                        name="stars"
                                                        value={data.stars}
                                                        className="mt-1 block w-full"
                                                        autoComplete="stars"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            
                                                <div className="w-1/2 mb-6 px-3">
                                                    <Label
                                                        forInput="rate"
                                                        value="درجه هتل"
                                                        className="mb-1"
                                                    />
                                                    <Select
                                                        name="rate"
                                                        value={data.rate}
                                                        className="mt-1 block w-full"
                                                        autoComplete="rate"
                                                        isFocused={true}
                                                        values={[
                                                            {
                                                                title: "عالی",
                                                                value: "عالی",
                                                            },
                                                            {
                                                                title: "خوب",
                                                                value: "خوب",
                                                            },
                                                            {
                                                                title: "متوسط",
                                                                value: "متوسط",
                                                            },
                                                        ]}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full mb-6 px-3">
                                                    <Label
                                                        forInput="type"
                                                        value="نوع اقامتگاه"
                                                        className="mb-1"
                                                    />
                                                    <Select
                                                        name="type"
                                                        value={data.type}
                                                        className="mt-1 block w-full"
                                                        autoComplete="type"
                                                        isFocused={true}
                                                        values={[
                                                            {
                                                                title: "هتل",
                                                                value: "hotel",
                                                            },
                                                            {
                                                                title: "آپارتمان",
                                                                value: "apartment",
                                                            },
                                                            {
                                                                title: "هتل آپارتمان",
                                                                value: "hotelApartment",
                                                            },
                                                        ]}
                                                        handleChange={(e) => {
                                                            onHandleChange(e);
                                                        }}
                                                    />
                                                </div>
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="address"
                                                        value="آدرس هتل"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="address"
                                                        value={data.address}
                                                        className="mt-1 block w-full"
                                                        autoComplete="address"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput=""
                                                        value={`موقعیت هتل (${latlng.lat} , ${latlng.lng})`}
                                                    />

                                                    <div
                                                        ref={mapContainer}
                                                        className={
                                                            "w-full h-[300px]"
                                                        }
                                                    ></div>
                                                </div>
                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="description"
                                                        value="توضیحات"
                                                    />

                                                    <RichText
                                                        value={data.description}
                                                        handleChange={(
                                                            data
                                                        ) => {
                                                            setData(
                                                                "description",
                                                                data
                                                            );
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="check_in"
                                                        value="ساعت ورود"
                                                    />

                                                    <Input
                                                        type="time"
                                                        name="check_in"
                                                        value={data.check_in}
                                                        className="mt-1 block w-full"
                                                        autoComplete="check_in"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="check_out"
                                                        value="ساعت خروج"
                                                    />

                                                    <Input
                                                        type="time"
                                                        name="check_out"
                                                        value={data.check_out}
                                                        className="mt-1 block w-full"
                                                        autoComplete="check_out"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full px-3 mb-6">
                                                    <Label
                                                        forInput="notes"
                                                        value="قوانین و مقررات"
                                                    />

                                                    <RichText
                                                        value={data.notes}
                                                        handleChange={(
                                                            data
                                                        ) => {
                                                            setData(
                                                                "notes",
                                                                data
                                                            );
                                                        }}
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="available_time_from"
                                                        value="تاریخ آزاد از "
                                                    />
                                                    <MobileDatePicker
                                                        defaultValue={
                                                            data.available_time_from
                                                        }
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.available_time_from
                                                        }
                                                        onChange={(
                                                            newValue
                                                        ) => {
                                                            onHandleChangeTimeFrom(
                                                                newValue
                                                            );
                                                        }}
                                                    />
                                                  
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="available_time_to"
                                                        value="تاریخ آزاد تا"
                                                    />
                                                    <MobileDatePicker
                                                        defaultValue={
                                                            data.available_time_to
                                                        }
                                                        className="border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full p-[9px] "
                                                        value={
                                                            data.available_time_to
                                                        }
                                                        onChange={(
                                                            newValue
                                                        ) => {
                                                            onHandleChangeTimeTo(
                                                                newValue
                                                            );
                                                        }}
                                                    />
                                                 
                                                </div>
                                                <div className="w-full mb-6 px-3">
                                                    <label
                                                        className="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                                        htmlFor="image"
                                                    >
                                                        تصویر اصلی هتل
                                                    </label>
                                                    {props.data &&
                                                        props.data?.image !=
                                                            "" && (
                                                            <img
                                                                src={
                                                                    props.data
                                                                        ?.image
                                                                        ?.url
                                                                }
                                                            />
                                                        )}
                                                    <input
                                                        className="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                                        type="file"
                                                        name="image"
                                                        id="image"
                                                        onChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-1/2 px-3 mb-6">
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
                                                        value="قیمت برای هر نوجوان(بین 6 تا ۱۲ سال)"
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
                                                        value="قیمت برای هر کودک(بین 2 تا 6 سال)"
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
                                                        value="قیمت برای هر نوزاد(تا 2 سال)"
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

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="fullboardPrice"
                                                        value="قیمت مازاد فول برد"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="fullboardPrice"
                                                        value={
                                                            data.fullboardPrice
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="fullboardPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="earlyCheckInPrice"
                                                        value="نرخ نیم شارژ بدو ورود"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="earlyCheckInPrice"
                                                        value={
                                                            data.earlyCheckInPrice
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="earlyCheckInPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="lateCheckOutPrice"
                                                        value="نرخ نیم شارژ خروج"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="lateCheckOutPrice"
                                                        value={
                                                            data.lateCheckOutPrice
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="lateCheckOutPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                {/* breakfast price */}
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="breakfastPrice"
                                                        value="نرخ صبحانه"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="breakfastPrice"
                                                        value={
                                                            data.breakfastPrice
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="breakfastPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                {/* lunch price */}
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="lunchPrice"
                                                        value="نرخ ناهار"
                                                    />

                                                    <Input
                                                        type="currency"
                                                        name="lunchPrice"
                                                        value={data.lunchPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="lunchPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>

                                                {/* dinner price */}
                                                <div className="w-full md:w-1/2 px-3 mb-6">
                                                    <Label
                                                        forInput="dinnerPrice"
                                                        value="نرخ شام"
                                                    />
                                                    <Input
                                                        type="currency"
                                                        name="dinnerPrice"
                                                        value={data.dinnerPrice}
                                                        className="mt-1 block w-full"
                                                        autoComplete="dinnerPrice"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>
                                            <p>امکانات هتل:</p>

                                            <div className=" flex flex-wrap w-full px-3 mb-6">
                                                {props.hotelServices.map(
                                                    (item) => (
                                                        <div
                                                            key={item.id}
                                                            className="block mt-4"
                                                        >
                                                            <label className="flex items-center">
                                                                <Checkbox
                                                                    name="hotelServices"
                                                                    checked={
                                                                        data.hotelServices.indexOf(
                                                                            item.id.toString()
                                                                        ) != -1
                                                                    }
                                                                    value={
                                                                        item.id
                                                                    }
                                                                    handleChange={
                                                                        onHandleChange
                                                                    }
                                                                />
                                                                <span className="ml-2 mr-2 text-sm text-gray-600">
                                                                    {item.name}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    )
                                                )}
                                            </div>
                                            <p>امکانات اتاق:</p>

                                            <div className=" flex flex-wrap w-full px-3 mb-6">
                                                {props.roomServices.map(
                                                    (item) => (
                                                        <div
                                                            key={item.id}
                                                            className="block mt-4"
                                                        >
                                                            <label className="flex items-center">
                                                                <Checkbox
                                                                    name="roomServices"
                                                                    checked={
                                                                        data.roomServices.indexOf(
                                                                            item.id.toString()
                                                                        ) != -1
                                                                    }
                                                                    value={
                                                                        item.id
                                                                    }
                                                                    handleChange={
                                                                        onHandleChange
                                                                    }
                                                                />
                                                                <span className="ml-2 mr-2 text-sm text-gray-600">
                                                                    {item.name}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    )
                                                )}
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
