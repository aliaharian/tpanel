import React, { useEffect, useState } from "react";
import Button from "@/Components/Button";
import Checkbox from "@/Components/Checkbox";
import Guest from "@/Layouts/Guest";
import Input from "@/Components/Input";
import Label from "@/Components/Label";
import ValidationErrors from "@/Components/ValidationErrors";
import { Head, Link, useForm } from "@inertiajs/inertia-react";
import GuestOtp from "@/Layouts/GuestOtp";

export default function LoginOtp({ status }) {
    const [currMobile, setCurrMobile] = useState("");
    const [errorTxt, setErrorTxt] = useState(null);
    const { data, setData, post, processing, errors, reset } = useForm({
        mobile: currMobile || "",
        code: "",
    });
    useEffect(() => {
        return () => {
            reset("mobile");
        };
    }, []);

    const onHandleChange = (event) => {
        setData(event.target.name, event.target.value);
    };

    const submit = async (e) => {
        e.preventDefault();
        if (currMobile) {
            post(route('confirmOtp'));

            // const response = await axios.post(route("confirmOtp"), data);
            // if (response.data.error) {
            //     setErrorTxt(response.data.error);
            // }
        } else {
            try {
                const response = await axios.post(route("doLoginOtp"), data);
                setData("mobile", response.data);
                setCurrMobile(response.data);
                setErrorTxt(null);
            } catch (e) {
                setErrorTxt("شماره موبایل صحیح نیست");
            }
        }
    };

    return (
        <GuestOtp>
            <Head title="Log in" />

            {errorTxt && (
                <div className="mb-4 font-medium text-sm text-red-600">
                    {errorTxt}
                </div>
            )}

            <ValidationErrors errors={errors} />

            <form onSubmit={submit}>
                {currMobile ? (
                    <div>
                        <Label forInput="code" value="کد تایید" />

                        <Input
                            type="tel"
                            name="code"
                            value={data.code}
                            className="mt-1 block w-full"
                            autoComplete="code"
                            isFocused={true}
                            handleChange={onHandleChange}
                        />
                    </div>
                ) : (
                    <div>
                        <Label forInput="mobile" value="شماره موبایل" />

                        <Input
                            type="tel"
                            name="mobile"
                            value={data.mobile}
                            className="mt-1 block w-full"
                            autoComplete="mobile"
                            isFocused={true}
                            handleChange={onHandleChange}
                        />
                    </div>
                )}
                <div className="flex items-center justify-end mt-4">
                    <Button className="ml-4" processing={processing}>
                        ارسال کد تایید
                    </Button>
                </div>
            </form>
        </GuestOtp>
    );
}
