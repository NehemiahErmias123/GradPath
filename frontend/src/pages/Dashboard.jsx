import { useState, useEffect } from "react";
import { Link } from "react-router-dom";

function Dashboard() {
  const [tracked, setTracked] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchTracked();
  }, []);

  const fetchTracked = () => {
    const token = localStorage.getItem("token");
    fetch("http://localhost/GradPath/api/tracked-programs/list.php", {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then((res) => res.json())
      .then((data) => {
        setTracked(data);
        setLoading(false);
      });
  };

  const untrackProgram = async (e, trackedProgramId) => {
    e.preventDefault();
    e.stopPropagation();

    const token = localStorage.getItem("token");
    await fetch("http://localhost/GradPath/api/tracked-programs/untrack.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ tracked_program_id: trackedProgramId }),
    });

    setTracked((prev) => prev.filter((item) => item.id !== trackedProgramId));
  };

  if (loading)
    return <p className="p-8 text-muted text-lg">Loading your dashboard...</p>;

  const totalPrograms = tracked.length;
  const totalItems = tracked.reduce((sum, t) => sum + Number(t.total_items), 0);
  const completedItems = tracked.reduce(
    (sum, t) => sum + Number(t.completed_items),
    0,
  );

  return (
    <div className="p-8 max-w-4xl mx-auto">
      <h1 className="font-serif text-4xl text-ink mb-2">Your applications</h1>
      <p className="text-muted text-base mb-10">
        Everything you're tracking, in one place.
      </p>

      <div className="flex gap-4 mb-12">
        <div className="bg-green-light rounded-xl px-6 py-5 flex-1">
          <p className="text-4xl font-serif text-green">{totalPrograms}</p>
          <p className="text-base text-ink mt-1">Programs tracked</p>
        </div>
        <div className="bg-brass-light rounded-xl px-6 py-5 flex-1">
          <p className="text-4xl font-serif text-brass">
            {completedItems}/{totalItems}
          </p>
          <p className="text-base text-ink mt-1">Checklist items done</p>
        </div>
      </div>

      {tracked.length === 0 && (
        <p className="text-muted text-lg">
          You haven't tracked any programs yet.
        </p>
      )}

      <div className="space-y-4">
        {tracked.map((item) => {
          const pct =
            item.total_items > 0
              ? Math.round((item.completed_items / item.total_items) * 100)
              : 0;

          return (
            <Link
              key={item.id}
              to={`/checklist/${item.id}`}
              className="block bg-white border border-hairline rounded-xl p-6 shadow-sm hover:shadow-lg hover:border-green transition-all duration-200"
            >
              <div className="flex justify-between items-start">
                <div>
                  <h2 className="font-serif text-2xl text-ink">
                    {item.program_name}
                  </h2>
                  <p className="text-base text-muted mt-1">
                    {item.university_name} — {item.country}
                  </p>
                </div>
                <div className="flex items-center gap-3">
                  <span className="text-sm font-medium text-white bg-brass rounded-full px-4 py-1.5">
                    {item.status}
                  </span>
                  <button
                    onClick={(e) => untrackProgram(e, item.id)}
                    className="text-sm text-muted hover:text-red-600 transition-colors duration-200"
                  >
                    Remove
                  </button>
                </div>
              </div>

              <div className="mt-5">
                <div className="flex justify-between text-sm text-muted mb-1.5">
                  <span>
                    {item.completed_items} of {item.total_items} items
                  </span>
                  <span>{pct}%</span>
                </div>
                <div className="w-full bg-hairline rounded-full h-2">
                  <div
                    className="bg-green h-2 rounded-full transition-all duration-300"
                    style={{ width: `${pct}%` }}
                  ></div>
                </div>
              </div>
            </Link>
          );
        })}
      </div>
    </div>
  );
}

export default Dashboard;
