import React from "react";
import Authenticated from "@/Layouts/Authenticated";
import { Head, useForm } from "@inertiajs/inertia-react";
import Label from "@/Components/Label";
import Input from "@/Components/Input";
import { useEffect } from "react/cjs/react.production.min";
import ValidationErrors from "@/Components/ValidationErrors";

export default function CreateHotelService(props) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: props.service?.name,
    });

    const onHandleChange = (event) => {
        setData(
            event.target.name,
            event.target.type === "checkbox"
                ? event.target.checked
                : event.target.value
        );
    };
    console.log(props.flash);

    const submit = (e) => {
        e.preventDefault();
        if (props.service) {
            put(route("hotelServices.update", props.service.id));
        } else {
            post(route("hotelServices.store"));
        }
        setData("name", "");
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    {props.service ? "ویرایش" : "تعریف"} سرویس هتل{" "}
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
                                        >
                                            <div className="flex flex-wrap -mx-3 mb-6">
                                                <div className="w-full px-3 mb-6 md:mb-0">
                                                    <Label
                                                        forInput="name"
                                                        value="نام سرویس"
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
