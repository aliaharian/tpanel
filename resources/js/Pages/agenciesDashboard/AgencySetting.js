import React, { useState } from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import ValidationErrors from "@/Components/ValidationErrors";
import AuthenticatedAgency from "@/Layouts/AuthenticatedAgency";
import Radio from "@/Components/Radio";

export default function AgencySetting(props) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        firstName: props.user.name || "",
        lastName: props.user.last_name || "",
        agencyName: props.agency?.agency_name || "",
        agencyLogo:  null,
        mobile: props.user.mobile || "",
        email: props.user.email || "",
        offPercent: props.agency?.agency_off_percent || "",
        markupPercent: props.agency?.agency_markup_percent || "",
        showLogo: props.agency?.showLogo == 1 ? "true" : "false",
    });

    const [logo, setLogo] = useState(
        "/uploads/" + props.agency?.agency_logo || null
    );
    const onHandleChange = (event) => {
        if (event.target.type === "file") {
            console.log("pipoipoii", event.target.files[0]);
            setData(event.target.name, event.target.files[0]);
            var fr = new FileReader();
            fr.onload = function () {
                setLogo(fr.result);
            };
            fr.readAsDataURL(event.target.files[0]);
        } else {
            setData(
                event.target.name,
                event.target.type === "checkbox"
                    ? event.target.checked
                    : event.target.value
            );
        }
    };

    console.log(props);

    const submit = (e) => {
        e.preventDefault();

        if (props.agency) {
            post(route("agencySetting.update"));
        }
    };
    return (
        <AuthenticatedAgency
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    تنظیمات آژانس {props.agency.agency_name}
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

                                    {
                                        props.flash.success && (
                                            <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                                <strong className="font-bold">موفقیت آمیز ! </strong>
                                                <span className="block sm:inline">{props.flash.success}</span>
                                            </div>
                                        )

                                    }
                                    <div className="overflow-hidden p-2 flex justify-center items-center">
                                        {/* <ValidationErrors errors={errors} /> */}

                                        <form
                                            className="w-full max-w-lg"
                                            onSubmit={submit}
                                        >
                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="firstName"
                                                        value="نام"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="firstName"
                                                        value={data.firstName}
                                                        className="mt-1 block w-full"
                                                        autoComplete="firstName"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="lastName"
                                                        value="نام خانوادگی"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="lastName"
                                                        value={data.lastName}
                                                        className="mt-1 block w-full"
                                                        autoComplete="lastName"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>

                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="offPercent"
                                                        value="درصد تخفیف"
                                                    />

                                                    <Input
                                                        type="number"
                                                        name="offPercent"
                                                        value={data.offPercent}
                                                        className="mt-1 block w-full"
                                                        autoComplete="offPercent"
                                                        isFocused={true}
                                                        readonly
                                                        // handleChange={
                                                        //     onHandleChange
                                                        // }
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="markupPercent"
                                                        value="درصد افزایش قیمت"
                                                    />

                                                    <Input
                                                        type="number"
                                                        name="markupPercent"
                                                        value={
                                                            data.markupPercent
                                                        }
                                                        className="mt-1 block w-full"
                                                        autoComplete="markupPercent"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>

                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="email"
                                                        value="ایمیل"
                                                    />

                                                    <Input
                                                        type="email"
                                                        name="email"
                                                        value={data.email}
                                                        className="mt-1 block w-full"
                                                        autoComplete="email"
                                                        isFocused={true}
                                                        readonly
                                                    />
                                                </div>
                                                <div className="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="mobile"
                                                        value="شماره موبایل"
                                                    />

                                                    <Input
                                                        type="tel"
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
                                            </div>

                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full px-3">
                                                    <Label
                                                        forInput="agencyName"
                                                        value="نام آژانس"
                                                    />

                                                    <Input
                                                        type="text"
                                                        name="agencyName"
                                                        value={data.agencyName}
                                                        className="mt-1 block w-full"
                                                        autoComplete="agencyName"
                                                        isFocused={true}
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                </div>
                                            </div>

                                            <div className="w-full mb-6">
                                                <label
                                                    className="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                                    htmlFor="agencyLogo"
                                                >
                                                    لوگو آژانس
                                                </label>
                                                {props.agency &&
                                                    props.agency?.agency_logo !=
                                                        "" && (
                                                        <img
                                                            src={
                                                                props.agency
                                                                    ?.logo?.url
                                                            }
                                                        />
                                                    )}
                                                <input
                                                    className="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                                    type="file"
                                                    name="agencyLogo"
                                                    id="agencyLogo"
                                                    onChange={onHandleChange}
                                                />
                                            </div>
                                            <div className="flex w-full px-3 mb-6">
                                                <label className="flex items-center ">
                                                    <Radio
                                                        name="showLogo"
                                                        value={"true"}
                                                        checked={
                                                            data.showLogo ==
                                                            "true"
                                                        }
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />
                                                    <span className="ml-2 mr-2 ml-2 text-sm text-gray-600">
                                                        نمایش نام و لوگوی آژانس
                                                    </span>
                                                </label>
                                                <label className="flex items-center">
                                                    <Radio
                                                        name="showLogo"
                                                        value={"false"}
                                                        checked={
                                                            data.showLogo ==
                                                            "false"
                                                        }
                                                        handleChange={
                                                            onHandleChange
                                                        }
                                                    />

                                                    <span className="ml-2 mr-2 ml-2 text-sm text-gray-600">
                                                        نمایش نام و لوگوی تورینو
                                                    </span>
                                                </label>
                                            </div>
                                            <button className="bg-blue-500 hover:bg-blue-700 w-full text-white font-bold py-2 px-4 rounded-full">
                                                {props.agency
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
        </AuthenticatedAgency>
    );
}
