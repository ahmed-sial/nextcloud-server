describe("TC-SI-06", () => {
  it("tests TC-SI-06", () => {
    cy.viewport(1089, 825);
    cy.visit("http://localhost:8080/login");
    cy.get("#user").click();
    cy.get("#user").type("admin'OR '1'='1");
    cy.get("#password").click();
      cy.get("#password").type("any");
      cy.get("fieldset > button svg").click();

  });
});
