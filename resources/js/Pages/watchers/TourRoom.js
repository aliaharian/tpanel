import Input from "@/Components/Input";
import Label from "@/Components/Label";

const TourRoom = ({ index, name, setName, capacity, setCapacity }) => {
    return (
        <div className="w-full">
            <div className="w-full px-3 mb-6">
                <Label
                    forInput={"room_name" + index}
                    value={"نام اتاق" + (index + 1)}
                />
                <Input
                    type="text"
                    name={"room_name" + index}
                    value={name}
                    className="mt-1 block w-full"
                    autoComplete={"room_name" + index}
                    isFocused={true}
                    handleChange={setName}
                />
            </div>
            <div className="w-full px-3 mb-6">
                <Label
                    forInput={"room_capcity" + index}
                    value={"ظرفیت اتاق" + (index + 1)}
                />
                <Input
                    type="number"
                    name={"room_capcity" + index}
                    value={capacity}
                    className="mt-1 block w-full"
                    autoComplete={"room_capcity" + index}
                    isFocused={true}
                    handleChange={setCapacity}
                />
            </div>
        </div>
    );
};

export default TourRoom;
