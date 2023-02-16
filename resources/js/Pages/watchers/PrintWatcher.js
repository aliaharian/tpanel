import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog";
import Input from "@/Components/Input";
import Label from "@/Components/Label";
import WatcherBox from "@/Components/WatcherBox";
import Authenticated from "@/Layouts/Authenticated";
import { Head } from "@inertiajs/inertia-react";
import moment from "jalali-moment";
import React from "react";
import WatcherContainer from "./WathcerContainer";

const PrintWatcher = ({ watcher, ...props }) => {
    const [openShareDialog, setOpenShareDialog] = React.useState(false);
    const [shareHash, setShareHash] = React.useState("");
    const [markupPercent, setMarkupPercent] = React.useState(
        watcher.agency
            ? watcher.markup || watcher.agency?.agency_markup_percent
            : 0
    );
    console.log("props", props);
    const renderTransport = (item) => {
        switch (item) {
            case "AIRPLANE":
                return "هواپیما";
            case "TRAIN":
                return "قطار";
            case "BUS":
                return "اتوبوس";
        }
    };
    console.log(watcher);
    const handlegenerateLink = async () => {
        const response = await axios.post(route("createLink"), {
            watcher: watcher.id,
        });
        setShareHash(response.data);
        console.log(response.data);
        setOpenShareDialog(true);
        // printWatcher/urbLOFXm15Hx1qpmqrKkPY2t
    };

    const timetoJalali = (time, onlyTime = false) => {
        moment.locale("fa", { useGregorianParser: true });
        if (onlyTime) {
            return moment(time).format("HH:mm");
        }
        return moment(time).format("jYYYY/jMM/jDD");
    };
    const handleSendSms = async () => {
        let url = `${props.baseUrl}/printWatcher/` + shareHash.url_hash;
        const response = await axios.post(route("sendWatcherLink"), {
            mobile: watcher.mobile_phone,
            link: url,
            name: watcher.buyer_name,
        });
        setOpenShareDialog(false);
    };

    const onHandleChange = (e) => {
        console.log(e.target.value);
        setMarkupPercent(e.target.value.replaceAll(",", ""));
    };
    const handleSave = async () => {
        await axios.post(route("saveWatcherMarkup"), {
            watcher_id: watcher.id,
            markup: parseFloat(markupPercent),
        });

        window.location.reload();
    };

    return (
        <WatcherContainer
            auth={props.auth}
            errors={props.errors}
            admin={props.admin}
            agency={props.agency}
        >
            <Head title="نمایش واچر" />

            <div className="flex py-3 flex-col items-center max-w-[750px] mx-auto">
                <ConfirmDeleteDialog
                    open={openShareDialog}
                    setOpen={setOpenShareDialog}
                    title={`آیا لینک به شماره ${watcher.mobile_phone} ارسال شود؟`}
                    text={`${props.baseUrl}/printWatcher/` + shareHash.url_hash}
                    handleDoAction={() => {
                        handleSendSms();
                    }}
                    actionText="ارسال پیامک"
                />
                <div className="w-full flex">
                    <div className="flex basis-3/12 border-r border-t border-b rounded-lg">
                        <div
                            className="basis-2/12 border-l h-full !bg-orange-400 rounded-r-lg border-r flex items-end justify-center py-4"
                            style={{ printColorAdjust: "exact" }}
                        >
                            <p className="writingMode text-xs text-white">
                                شماره پشتیبانی: 09121111111
                            </p>
                        </div>
                        <div className="basis-10/12 px-2 py-3 border-l border-dashed rounded-l-lg">
                            <div className="mb-4">
                                <p className="text-xxs text-gray-600">
                                    نام سرپرست مسافران:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {watcher.buyer_name}
                                </p>
                            </div>
                            <div className="mb-4">
                                <p className="text-xxs text-gray-600">
                                    کد ملی:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {watcher.buyer_national_code}
                                </p>
                            </div>
                            <div className="mb-4">
                                <p className="text-xxs text-gray-600">
                                    شماره تماس:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {watcher.mobile_phone}
                                </p>
                            </div>
                            <div className="mb-20">
                                <p className="text-xxs text-gray-600">
                                    تعداد مسافران:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {watcher.people_count} نفر
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="basis-9/12 rounded-lg border-t border-b border-l">
                        <div className="flex justify-between w-full border-b  px-2 py-3 ">
                            <div className="flex">
                                <p className="text-xxs ml-2 text-gray-600">
                                    شماره رزرو:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {watcher.id}
                                </p>
                            </div>
                            <div className="flex">
                                <p className="text-xxs ml-2 text-gray-600">
                                    تاریخ خرید:{" "}
                                </p>
                                <p className="text-xs font-medium">
                                    {timetoJalali(watcher.created_at)}
                                </p>
                            </div>
                        </div>
                        <div className="flex">
                            <div className="basis-9/12">
                                <div className="flex w-full flex-wrap px-2 py-3 border-b">
                                    <div className=" basis-1/3 mb-5">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            شهر مبدا:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.from_city.title}
                                        </p>
                                    </div>
                                    <div className=" basis-1/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            شهر مقصد:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.to_city.title}
                                        </p>
                                    </div>
                                    <div className=" basis-1/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            تعداد شب اقامت:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.stay_length} شب
                                        </p>
                                    </div>
                                    <div className=" basis-1/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            تاریخ رفت:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {new Date(
                                                parseInt(
                                                    watcher.departure_vehicle
                                                        .departure_date_time
                                                )
                                            ).toLocaleDateString("fa-IR")}
                                        </p>
                                    </div>
                                    <div className="">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            تاریخ برگشت:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {new Date(
                                                parseInt(
                                                    watcher.arrival_vehicle
                                                        .departure_date_time
                                                )
                                            ).toLocaleDateString("fa-IR")}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex w-full flex-wrap px-2 py-3 basis-9/12 border-b">
                                    <div className=" basis-2/3 mb-5">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            نام هتل:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.hotel_name}
                                        </p>
                                    </div>
                                    <div className=" basis-1/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            تعداد اتاق:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.room_numbers}
                                        </p>
                                    </div>
                                    <div className=" basis-2/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            نوع اتاق:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.room_type}
                                        </p>
                                    </div>
                                    <div className=" basis-1/3">
                                        <p className="text-xxs ml-2 text-gray-600">
                                            نوع پذیرایی:{" "}
                                        </p>
                                        <p className="text-xs font-medium">
                                            {watcher.fullboard == 1 &&
                                                "فول برد"}{" "}
                                            {watcher.breakfast == 1 && "صبحانه"}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex w-full border-b">
                                    <div className="basis-1/2 px-2 py-3 border-l">
                                        <div className="flex items-center justify-start">
                                            <div className="ml-4 flex flex-col items-center justify-center">
                                                <img
                                                    className="max-w-[40px]"
                                                    src={
                                                        watcher
                                                            .departure_vehicle
                                                            .transport_company
                                                            .logo.url
                                                    }
                                                />
                                                <p className="text-xs">
                                                    {
                                                        watcher
                                                            .departure_vehicle
                                                            .transport_company
                                                            .name
                                                    }
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-gray-500">
                                                    اطلاعات رفت
                                                </p>
                                                <p className="text-xs">
                                                    {renderTransport(
                                                        watcher
                                                            .departure_vehicle
                                                            .transport_type
                                                    )}
                                                </p>
                                                <p className="text-lg font- bold">
                                                    {timetoJalali(
                                                        parseInt(
                                                            watcher
                                                                .departure_vehicle
                                                                .departure_date_time
                                                        ),
                                                        true
                                                    )}
                                                </p>

                                                <p className="text-xs">
                                                    {" "}
                                                    {new Date(
                                                        parseInt(
                                                            watcher
                                                                .departure_vehicle
                                                                .departure_date_time
                                                        )
                                                    ).toLocaleDateString(
                                                        "fa-IR"
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="basis-1/2 px-2 py-3">
                                        <div className="flex items-center justify-start">
                                            <div className="ml-4 flex flex-col items-center justify-center">
                                                <img
                                                    className="max-w-[40px]"
                                                    src={
                                                        watcher.arrival_vehicle
                                                            .transport_company
                                                            .logo.url
                                                    }
                                                />
                                                <p className="text-xs">
                                                    {
                                                        watcher.arrival_vehicle
                                                            .transport_company
                                                            .name
                                                    }
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-gray-500">
                                                    اطلاعات برگشت
                                                </p>
                                                <p className="text-xs">
                                                    {renderTransport(
                                                        watcher.arrival_vehicle
                                                            .transport_type
                                                    )}
                                                </p>
                                                <p className="text-lg font- bold">
                                                    {timetoJalali(
                                                        parseInt(
                                                            watcher
                                                                .arrival_vehicle
                                                                .departure_date_time
                                                        ),
                                                        true
                                                    )}
                                                </p>

                                                <p className="text-xs">
                                                    {" "}
                                                    {new Date(
                                                        parseInt(
                                                            watcher
                                                                .arrival_vehicle
                                                                .departure_date_time
                                                        )
                                                    ).toLocaleDateString(
                                                        "fa-IR"
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {watcher.services.length > 0 && (
                                    <div className="flex w-full flex-wrap px-2 pt-3">
                                        {watcher.services.map((item, index) => (
                                            <div className="flex mb-3">
                                                <svg
                                                    fill="#000000"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    width="16px"
                                                    height="16px"
                                                >
                                                    <path d="M 20.292969 5.2929688 L 9 16.585938 L 4.7070312 12.292969 L 3.2929688 13.707031 L 9 19.414062 L 21.707031 6.7070312 L 20.292969 5.2929688 z" />
                                                </svg>
                                                <p
                                                    key={index}
                                                    className="mr-2 ml-5 text-xxs"
                                                    style={{
                                                        textWrap: "noWrap",
                                                    }}
                                                >
                                                    {item.name}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="basis-3/12 border-r px-2 py-3">
                                {watcher.agency?.agency_logo &&
                                props.showLogo == 1 ? (
                                    <div className="flex flex-col items-center justify-center">
                                        <img
                                            className="max-w-[150px]"
                                            src={watcher.agency?.logo?.url}
                                        />
                                        <h1 className="text-sm font-medium mb-8 mt-2 text-center">
                                            {watcher.agency?.agency_name}
                                        </h1>
                                    </div>
                                ) : (
                                    <h1 className="text-sm font-medium mb-8">
                                        شرکت خدمات مسافرتی تورینو
                                    </h1>
                                )}

                                <p className="text-xxs text-gray-600">
                                    قیمت تور برای هر نفر بزرگسال:{" "}
                                </p>
                                <p className="text-sm mt-1 mb-10">
                                    {props.pricePerAdult} ریال
                                </p>
                                {watcher.agency &&
                                (props.admin || props.agency) ? (
                                    <>
                                        <p className="text-xxs text-gray-600 text-red-500">
                                            مبلغ قابل پرداخت برای آژانس:
                                        </p>
                                        <p className="text-sm mt-1 mb-10">
                                            {props.agencyPrice} ریال
                                        </p>

                                        <p className="text-xxs text-gray-600 text-red-500">
                                            مبلغ قابل پرداخت برای مشتری:
                                        </p>
                                        <p className="text-sm mt-1 mb-10 ">
                                            {props.userPrice} ریال
                                        </p>
                                    </>
                                ) : (
                                    <>
                                        <p className="text-xxs text-gray-600 ">
                                            مبلغ قابل پرداخت:
                                        </p>
                                        <p className="text-sm mt-1">
                                            {props.totalPrice} ریال
                                        </p>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
                <WatcherBox className="mt-4" title="قوانین تور">
                    <div className="flex flex-wrap justify-start">
                        <ul>
                            <li className="text-sm">
                                - زمان حضور مسافر در ترمینال یا راه آهن یا
                                فرودگاه یک ساعت و نیم قبل از ساعت حرکت میباشد .
                            </li>
                            <li className="text-sm">
                                - تحویل اتاق در مقصد ساعت 14 و تخلیه اتاق ساعت
                                12 میباشد .
                            </li>
                            <li className="text-sm">
                                - ساعات سرو غذا (صبحانه – ناهار – شام ) را از
                                پذیرش هتل استعالم فرمایید و طبق آن ساعات برنامه
                                ریزی فرمایید .
                            </li>
                            <li className="text-sm">
                                - همراه داشتن کارت ملی و شناسنامه کلیه مسافرین
                                در طول سفر الزامی است .
                            </li>
                            <li className="text-sm">
                                - چنانچه از خدمات ترانسفر استفاده میکنید شماره
                                تلفن همراه اعالمی باید پاسخگو باشد .
                            </li>
                            <li className="text-sm">
                                - جریمه کنسلی تور تا یک هفته قبل از حرکت 20 درصد
                                – تا 48 ساعت قبل از حرکت 50 درصد و بعد از آن 80
                                درصد مبلغ تور میباشد .
                            </li>

                            <li className="text-sm">
                                - رعایت کلیه قوانین حاکم بر وسائط نقلیه
                                (اتوبوس،قطار و پرواز ) و هتلهای مقصد برای مسافر
                                الزامی است و این شرکت تابع آن میباشد .
                            </li>
                            <li className="text-sm">
                                - این شرکت تنها متعهد به ارائه خدمات مندرج در
                                این برگه میباشد .لذا تعهد اخذ هر گونه خدمات خارج
                                از این لیست که توسط هتل یا هر شخص دیگری ارائه
                                میشود و پرداخت هزینه های مرتب بر عهده مسافر
                                خواهد بود .
                            </li>
                            <li className="text-sm">
                                - این برگه به منزله صورتحساب و رسید پرداخت وجه
                                معتبر بوده و مسافر میتواند پیگیر اخذ خدمات مندرج
                                از شرکت باشد
                            </li>
                        </ul>
                    </div>
                </WatcherBox>
                {watcher.agency && props.agency && (
                    <div className="flex w-full mb-5">
                        <div className="w-full md:w-full px-3 mb-6 md:mb-0">
                            <Label
                                forInput="markupPercent"
                                value="درصد یا مبلغ افزایش قیمت"
                            />

                            <Input
                                type="currency"
                                name="markupPercent"
                                value={markupPercent}
                                className="mt-1 block w-full"
                                autoComplete="markupPercent"
                                isFocused={true}
                                handleChange={onHandleChange}
                                percentOrPrice
                            />
                        </div>
                    </div>
                )}

                <div className="flex">
                    {props.admin && (
                        <button
                            className="bg-cyan-400 p-4 rounded-lg text-white"
                            onClick={handlegenerateLink}
                        >
                            ساخت لینک دانلود
                        </button>
                    )}
                    {watcher.agency && props.agency && (
                        <button
                            className="bg-green-600 p-4 rounded-lg text-white mr-2"
                            onClick={handleSave}
                        >
                            بروزرسانی قیمت
                        </button>
                    )}
                </div>
            </div>
        </WatcherContainer>
    );
};

export default PrintWatcher;
