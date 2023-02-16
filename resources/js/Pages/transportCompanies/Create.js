import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import { useEffect } from "react/cjs/react.production.min";
import ValidationErrors from "@/Components/ValidationErrors";
import Select from "@/Components/Select";
import { Inertia } from "@inertiajs/inertia";

export default function CreateTransportCompany(props) {
    const [uploadDone , setUploadDone] = React.useState(true);
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: props.co?.name || "",
        logo: null,
        currLogo: props.co?.logo || "",
        active: props.co?.active || 1,
        type: props.co?.transport_type || null,
    });

    const onHandleChange = (event) => {
        if (event.target.type === "file") {
            // setUploadDone(false)
            console.log("pipoipoii", event.target.files[0]);
            setData(event.target.name, event.target.files[0]);

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

        if (props.co) {
            // Inertia.post(`/users/${user.id}`, {
            //     _method: "put",
            //     avatar: form.avatar,
            // });
            Inertia.post(route("transportCompanies.update", props.co.id), {
                _method: "put",
                name: data.name,
                logo: data.logo,
                type: data.type,
                active: data.active,
            });
        } else {
            post(route("transportCompanies.store"));
        }
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {props.co ? "ویرایش" : "ایجاد"} شرکت حمل و نقل{" "}
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
                                                <div className="w-full md:w-full px-3 mb-6 md:mb-0">
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
                                            </div>
                                            <div className="w-full mb-6">
                                                <Label
                                                    forInput="type"
                                                    value="نوع شرکت"
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
                                                    handleChange={
                                                        onHandleChange
                                                    }
                                                />
                                            </div>

                                            <div className="w-full mb-6">
                                                <label
                                                    className="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                                    htmlFor="logo"
                                                >
                                                    لوگو شرکت حمل و نقل
                                                </label>
                                                {props.co &&
                                                    props.co?.logo != "" && (
                                                        <img
                                                            src={
                                                                props.co?.logo
                                                                    ?.url
                                                            }
                                                        />
                                                    )}
                                                <input
                                                    className="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                                    type="file"
                                                    name="logo"
                                                    id="logo"
                                                    onChange={onHandleChange}
                                                />
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
