import { useState, useEffect } from "react";
import { useParams } from "react-router-dom";

function Checklist() {
  const { trackedProgramId } = useParams();
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [newItemTitle, setNewItemTitle] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("token");
    fetch(
      `http://localhost/GradPath/api/checklist-items/list.php?tracked_program_id=${trackedProgramId}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    )
      .then((res) => res.json())
      .then((data) => {
        setItems(data);
        setLoading(false);
      });
  }, [trackedProgramId]);

  const toggleItem = async (itemId) => {
    const token = localStorage.getItem("token");
    const response = await fetch(
      "http://localhost/GradPath/api/checklist-items/toggle.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ item_id: itemId }),
      },
    );
    const data = await response.json();

    setItems((prev) =>
      prev.map((item) =>
        item.id === itemId
          ? { ...item, is_completed: data.is_completed }
          : item,
      ),
    );
  };

  const addItem = async (e) => {
    e.preventDefault();
    if (!newItemTitle.trim()) return;

    const token = localStorage.getItem("token");
    const response = await fetch(
      "http://localhost/GradPath/api/checklist-items/create.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          tracked_program_id: trackedProgramId,
          title: newItemTitle,
        }),
      },
    );
    const data = await response.json();

    setItems((prev) => [
      ...prev,
      { id: data.id, title: newItemTitle, is_completed: 0 },
    ]);
    setNewItemTitle("");
  };

  if (loading)
    return <p className="p-8 text-muted text-lg">Loading checklist...</p>;

  return (
    <div className="p-8 max-w-3xl mx-auto">
      <h1 className="font-serif text-4xl text-ink mb-8">Checklist</h1>

      <div className="space-y-3">
        {items.map((item) => (
          <label
            key={item.id}
            className="flex items-center gap-4 border border-hairline rounded-lg p-4 cursor-pointer hover:border-green transition-colors duration-200"
          >
            <input
              type="checkbox"
              checked={!!item.is_completed}
              onChange={() => toggleItem(item.id)}
              className="w-5 h-5 accent-green"
            />
            <span
              className={`text-base ${item.is_completed ? "line-through text-muted" : "text-ink"}`}
            >
              {item.title}
            </span>
          </label>
        ))}
      </div>

      <form onSubmit={addItem} className="mt-8 flex gap-3">
        <input
          type="text"
          placeholder="Add a custom item..."
          value={newItemTitle}
          onChange={(e) => setNewItemTitle(e.target.value)}
          className="flex-1 border border-hairline px-4 py-3 rounded-lg text-base focus:outline-none focus:border-green"
        />
        <button
          type="submit"
          className="bg-green text-white px-6 py-3 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105 hover:opacity-90"
        >
          Add
        </button>
      </form>
    </div>
  );
}

export default Checklist;
