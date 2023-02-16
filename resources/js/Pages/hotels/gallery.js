//HotelGallery component
import React from "react";
import { Inertia } from "@inertiajs/inertia";
import { Head } from "@inertiajs/inertia-react";
import Authenticated from "@/Layouts/Authenticated";

export default function HotelGallery(props) {
    console.log(props);
    const deleteFile = (image) => {
        Inertia.delete(route("deleteFile", image.id));
    };
    return (
        <Authenticated
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    تصاویر هتل  {props.hotel.name}
                </h2>
            }
        >
            <Head title="Dashboard" />
            <div className="p-6">
                <div className="flex flex-col p-6">
                    {/* upload image form */}
                    <p className="mb-3 text-xl font-bold">آپلود تصویر جدید</p>
                    <form
                        onSubmit={(e) => {
                            //check if file selected
                            e.preventDefault();
                            if (e.target.image.files[0]) {
                                const data = new FormData(e.target);
                                Inertia.post(
                                    route("saveFile", props.hotel.id),
                                    data
                                );
                                //clear input
                                e.target.reset();
                            }
                        }}
                    >
                        <input type="file" name="image" />
                        <button
                            className="bg-blue-400 px-5 py-2 rounded-xl text-white"
                            type="submit"
                        >
                            Upload
                        </button>
                    </form>
                </div>
                <div className="flex flex-wrap">
                    {props?.images?.map((image, index) => (
                        <div
                            key={index}
                            className="basis-1/4 h-[300px] p-6 rounded-3xl"
                        >
                            <div className="w-full h-full relative cursor-pointer rounded-3xl">
                                <img
                                    src={image.file.url}
                                    className="w-full h-full object-cover rounded-3xl"
                                />
                                <div
                                    onClick={() => deleteFile(image)}
                                    className="absolute top-0 left-0 w-full h-full bg-[rgba(0,0,0,0.4)] flex items-center justify-center rounded-3xl"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth={1.5}
                                        stroke="white"
                                        className="w-6 h-6"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </Authenticated>
    );
}
